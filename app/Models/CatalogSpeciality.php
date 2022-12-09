<?php

namespace App\Models;

use App\Traits\Catalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Model;
use App\ExternalServices\Asu\Profession;
use App\Traits\HasAsuDivisionsNameTrait;
use App\Policies\CatalogSpecialityPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogSpeciality extends Model
{
    use HasFactory, Catalog, HasAsuDivisionsNameTrait, \Bkwld\Cloner\Cloneable;

    protected $cloneable_relations = ['subjects'];

    const SPECIALITY = 2;

    protected $table = 'catalog_subjects';

    protected $fillable = [
        'selective_discipline_id',
        'catalog_education_level_id',
        'user_id',
        'speciality_id',
        'faculty_id',
        'department_id',
        'year',
        'need_verification',
    ];

    protected $casts = [
        'need_verification' => 'boolean'
    ];

    public function subjects()
    {
        return $this->hasMany(SpecialitySubject::class, 'catalog_subject_id', 'id');
    }

    public function signatures()
    {
        return $this->hasMany(CatalogSignature::class, 'catalog_subject_id', 'id');
    }

    public function getSpecialityIdNameAttribute()
    {
        if (!$this->speciality_id) return null;

        $professions = new Profession();

        $code = $professions->getTitle($this->speciality_id, 'code');
        return "{$code} {$professions->getTitle($this->speciality_id, 'title')}";
    }

    public function getSpecialityCatalogNameAttribute()
    {
        $nextYear = $this->year + 1;
        return "Каталог {$this->year}-{$nextYear}р. за спеціальністю {$this->getSpecialityIdNameAttribute()}";
    }

    public function verifications()
    {
        return $this->hasMany(CatalogVerification::class, 'catalog_subject_id', 'id');
    }

    public function owners()
    {
        return $this->hasMany(OwnerCatalogSubject::class, 'catalog_subject_id', 'id');
    }

    public function scopeFilterBy($query, $filters)
    {
        // $namespace = 'App\Helpers\Filters\CatalogSubjectFilters';
        $namespace = 'App\Helpers\Filters\CatalogSpecialityFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }

    public function actions()
    {
        $policy = new CatalogSpecialityPolicy();
        $user = Auth::user();

        return [
            'copy' =>  Gate::allows('copy-catalog-speciality'),
            'preview' => $policy->viewAny($user),
            'edit' => $policy->update($user, $this),
            'delete' => Gate::allows('delete-catalog-speciality', $this),
        ];
    }

    protected static function booted()
    {
        static::saving(function ($catalog) {
            // $catalog->user_id = 1; // TODO: Auth::id();
            $catalog->user_id = Auth::id();
        });

        static::addGlobalScope('selective_discipline', function (Builder $builder) {
            $builder->where('selective_discipline_id', self::SPECIALITY);
        });
    }
}
