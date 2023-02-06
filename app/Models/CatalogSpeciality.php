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

    public function getSpecialityCatalogNameAttribute()
    {
        return nl2br("Каталог {$this->years()}\n за спеціальністю {$this->getSpecialityIdNameAttribute()}\n {$this->educationLevel->title}");
    }

    public function getStatusAttribute()
    {
        $fullVerification = VerificationStatuses::fullCatalogSpecialityVerification();

        $data = array_column($this->verifications->toArray(), 'status');

        if (count($this->filterStatus($data, 1)) === $fullVerification) {
            $result = 'success';
        } elseif (count($data) > 0 && count($this->filterStatus($data, 0)) == 0) {
            $result = 'warning';
        } elseif (count($data) > 0 && count($this->filterStatus($data, 0)) >= 0) {
            $result = 'error';
        } else {
            $result = '';
        }
        return $result;
    }

    private function filterStatus($data, $id)
    {
        return array_filter($data, function ($val) use ($id) {
            return $val === $id;
        });
    }

    public function verifications()
    {
        return $this->hasMany(CatalogVerification::class, 'catalog_id', 'id');
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
            // 'edit' => $policy->update($user, $this),
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
