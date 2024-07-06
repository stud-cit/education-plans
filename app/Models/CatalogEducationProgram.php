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
        'speciality_id',
        'user_id',
        'faculty_id',
        'department_id',
        'year',
        'need_verification',
    ];

    protected $casts = [
        'need_verification' => 'boolean',
        'year' => 'integer',
    ];

    public function subjects()
    {
        return $this->hasMany(EducationProgramSubject::class, 'catalog_subject_id', 'id');
    }

    public function signatures()
    {
        return $this->hasMany(CatalogSignature::class, 'catalog_subject_id', 'id');
    }

    public function getEducationProgramCatalogNameAttribute()
    {
        return nl2br("Каталог {$this->years()}\nза освітньою програмою {$this->getEducationProgramIdNameAttribute()}\n{$this->educationLevel->title}");
    }

    public function verifications()
    {
        return $this->hasMany(CatalogVerification::class, 'catalog_id', 'id');
    }

    public function owners()
    {
        return $this->hasMany(OwnerCatalogSubject::class, 'catalog_subject_id', 'id');
    }

    public function scopeVerified($query)
    {
        $query->whereHas('verifications', function (Builder $query) {
            $query->where('status', true);
        }, '>=', VerificationStatuses::fullCatalogEducationProgramVerification());
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\CatalogEducationProgramFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }

    public function getStatusAttribute()
    {
        $fullVerification = VerificationStatuses::fullCatalogEducationProgramVerification();

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

    public function actions()
    {
        $policy = new CatalogEducationProgramPolicy();
        $user = Auth::user();

        return [
            'copy' =>  Gate::allows('copy-catalog-education-program'),
            'preview' => $policy->viewAny($user),
            'delete' => Gate::allows('delete-catalog-education-program', $this),
        ];
    }

    public function isVerified(): bool
    {
        return $this->status === 'success';
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
