<?php

namespace App\Models;

use App\Models\Teacher;
use App\Models\CatalogSubject;
use App\Models\VerificationStatuses;
use Illuminate\Support\Facades\Auth;
use App\Models\CatalogEducationLevel;
use App\ExternalServices\Asu\Subjects;
use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAsuDivisionsNameTrait;
use App\Policies\CatalogSelectiveSubjectPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogSelectiveSubject extends Model
{
    use HasFactory, HasAsuDivisionsNameTrait;

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

    public function getSubjectNameAttribute()
    {
        if (!$this->asu_id) return null;

        return $this->subject()->getTitle($this->asu_id, 'title') . " ({$this->getEnglishSubjectNameAttribute()})";
    }

    public function getEnglishSubjectNameAttribute()
    {
        $engTitle = $this->subject()->getEnglishTitle($this->asu_id);

        if ($this->title_en === null) {
            return $engTitle;
        }

        return $this->title_en === $engTitle ? $engTitle : $this->title_en;
    }

    public function getListFieldsKnowledgeNameAttribute()
    {
        $obj = json_decode($this->list_fields_knowledge);

        if ($obj->list === null) {
            return $obj->label;
        }

        $label = "$obj->label $obj->type_name ";

        $array = array_map(function ($item) {
            if (array_key_exists('name', (array)$item)) {
                return $item->name;
            }
            if (array_key_exists('title', (array)$item)) {
                return $item->title;
            }
        }, $obj->list);

        return $label . implode(', ', $array);
    }

    public function getLimitationNameAttribute()
    {
        $obj = json_decode($this->limitation);

        if ($obj->semesters === null) {
            return $obj->label;
        }

        return $obj->label . ' ' . implode(', ', $obj->semesters);
    }

    protected function subject()
    {
        return new Subjects();
    }

    public function educationLevel()
    {
        return $this->belongsTo(CatalogEducationLevel::class, 'catalog_education_level_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function catalog()
    {
        return $this->belongsTo(CatalogSubject::class, 'catalog_subject_id', 'id');
    }

    public function languages()
    {
        return $this->hasMany(LanguageSubject::class, 'subject_id', 'id');
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function verifications()
    {
        return $this->hasMany(SubjectVerification::class, 'subject_id', 'id');
    }

    public function lecturers()
    {
        return $this->teachers()->where('type', Teacher::LECTOR)->select('id', 'catalog_selective_subject_id', 'asu_id');
    }

    public function practice()
    {
        return $this->teachers()->where('type', Teacher::PRACTICE)->select('id', 'catalog_selective_subject_id', 'asu_id');
    }

    public function lecturersSave($teachers)
    {
        $lectures = array_map(function ($teacher) {
            $teacher['type'] = Teacher::LECTOR;
            return $teacher;
        }, $teachers);

        return $this->teachers()->createMany($lectures);
    }

    public function practiceSave($teachers)
    {
        $lectures = array_map(function ($teacher) {
            $teacher['type'] = Teacher::PRACTICE;
            return $teacher;
        }, $teachers);

        return $this->teachers()->createMany($lectures);
    }

    /**
     * Get first catalog Вибіркові дисципліни (каталог)
     */
    public function selectiveCatalog()
    {
        return $this->catalog()->where('selective_discipline_id', 1);
    }

    /**
     * Get second catalog Вибіркові дисципліни за спеціальністю
     */
    public function specialtiesCatalog()
    {
        return $this->catalog()->where('selective_discipline_id', 2);
    }

    /**
     * Get third catalog Вибіркові дисципліни за освітньою програмою
     */
    public function educationProgramCatalog()
    {
        return $this->catalog()->where('selective_discipline_id', 3);
    }

    public function scopePublished($query)
    {
        return $query->where('published', 1);
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

    public function updateTeachers($records, $type)
    {
        foreach ($records as $lecture) {
            if (!array_key_exists('type', $lecture)) {
                $lecture['type'] = $type;
            }
            if (array_key_exists('full_name', $lecture)) {
                unset($lecture['full_name']);
            }
            $this->teachers()->updateOrCreate($lecture);
        }
    }

    protected static function booted()
    {
        static::creating(function ($catalog) {
            $catalog->user_id = Auth::id();
        });
    }
}
