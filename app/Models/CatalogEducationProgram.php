<?php

namespace App\Models;

use App\Traits\Catalog;
use App\Models\CatalogSignature;
use App\Models\CatalogVerification;
use App\Models\OwnerCatalogSubject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Helpers\Filters\FilterBuilder;
use App\Models\EducationProgramSubject;
use Illuminate\Database\Eloquent\Model;
use App\ExternalServices\Asu\Profession;
use App\Traits\HasAsuDivisionsNameTrait;
use Illuminate\Database\Eloquent\Builder;
use App\Policies\CatalogEducationProgramPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogEducationProgram extends Model
{
    use HasFactory, Catalog, HasAsuDivisionsNameTrait, \Bkwld\Cloner\Cloneable;

    protected $cloneable_relations = ['subjects'];

    protected $table = 'catalog_subjects';

    const EDUCATION_PROGRAM = 3;

    protected $fillable = [
        'selective_discipline_id',
        'catalog_education_level_id',
        'education_program_id',
        'user_id',
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
        return $this->hasMany(EducationProgramSubject::class, 'catalog_subject_id', 'id');
    }

    public function signatures()
    {
        return $this->hasMany(CatalogSignature::class, 'catalog_subject_id', 'id');
    }

    public function getEducationProgramIdNameAttribute()
    {
        if (!$this->education_program_id) return null;

        $professions = new Profession();
        return $professions->getTitle($this->education_program_id, 'title');
    }

    public function getEducationProgramCatalogNameAttribute()
    {
        $nextYear = $this->year + 1;
        return "Каталог {$this->year}-{$nextYear}р. за освітньою програмою {$this->getEducationProgramIdNameAttribute()}";
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
        $namespace = 'App\Helpers\Filters\CatalogEducationProgramFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }

    public function actions()
    {
        $policy = new CatalogEducationProgramPolicy();
        $user = Auth::user();

        return [
            'copy' =>  Gate::allows('copy-catalog-education-program'),
            'preview' => $policy->viewAny($user),
            // 'edit' => $policy->update($user, $this),
            // check if works
            'delete' => Gate::allows('delete-catalog-education-program', $this),
        ];
    }

    protected static function booted()
    {
        static::saving(function ($catalog) {
            $catalog->user_id = Auth::id();
        });

        static::addGlobalScope('selective_discipline', function (Builder $builder) {
            $builder->where('selective_discipline_id', self::EDUCATION_PROGRAM);
        });
    }
}
