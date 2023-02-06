<?php

namespace App\Models;

use App\Traits\Subject;
use App\Models\EducationLevel;
use App\Models\VerificationStatuses;
use Illuminate\Support\Facades\Auth;
use App\Models\CatalogEducationLevel;
use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAsuDivisionsNameTrait;
use App\Policies\CatalogSelectiveSubjectPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogSelectiveSubject extends Model
{
    use HasFactory, HasAsuDivisionsNameTrait, Subject;

    protected $fillable = [
        'id',
        'asu_id',
        'catalog_subject_id',
        'faculty_id',
        'department_id',
        'catalog_education_level_id',
        'user_id',
        'title',
        'title_en',
        'list_fields_knowledge',
        'types_educational_activities',
        'general_competence',
        'learning_outcomes',
        'number_acquirers',
        'entry_requirements_applicants',
        'limitation',
        'published',
        'need_verification'
    ];

    protected $casts = [
        'published' => 'boolean',
        'need_verification' => 'boolean'
    ];

    public function getStatusAttribute()
    {
        $fullVerification = VerificationStatuses::fullSubjectVerification();

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

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class, 'catalog_education_level_id')->withTrashed();
    }

    public function verifications()
    {
        return $this->hasMany(SubjectVerification::class, 'subject_id', 'id');
    }

    /**
     * Get first catalog Вибіркові дисципліни (каталог)
     */
    public function selectiveCatalog()
    {
        return $this->catalog()->where('selective_discipline_id', 1);
    }

    public function scopeOfUserType($query, $type)
    {
        switch ($type) {
            case User::TRAINING_DEPARTMENT:
            case User::PRACTICE_DEPARTMENT:
            case User::EDUCATIONAL_DEPARTMENT_DEPUTY:
            case User::EDUCATIONAL_DEPARTMENT_CHIEF:
            case User::FACULTY_INSTITUTE:
                return $query->published();

            case User::DEPARTMENT:
                return $query->published()
                    ->orWhere(function ($query) {
                        $query->where('user_id', Auth::id())->where('published', false);
                    });

            default:
                return $query;
        }
    }

    public function actions()
    {
        $policy = new CatalogSelectiveSubjectPolicy();
        $user = Auth::user();

        return [
            'preview' => $policy->viewAny($user),
            'edit' => $policy->update($user, $this),
            'delete' => $policy->delete($user, $this),
        ];
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\CatalogSelectiveSubjectFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }

    protected static function booted()
    {
        static::creating(function ($catalog) {
            $catalog->user_id = Auth::id();
        });
    }
}
