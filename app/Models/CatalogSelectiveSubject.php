<?php

namespace App\Models;

use App\Models\User;
use App\Models\CatalogSubject;
use App\Models\CatalogEducationLevel;
use App\ExternalServices\Asu\Subjects;
use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAsuDivisionsNameTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogSelectiveSubject extends Model
{
    use HasFactory, HasAsuDivisionsNameTrait;

    protected $fillable = [
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
        'published'
    ];
    protected $casts = [
        'published' => 'boolean'
    ];

    const LECTOR = 'lector';
    const PRACTICE = 'practice';

    public function getSubjectNameAttribute()
    {
        if (!$this->asu_id) return null;

        return $this->subject()->getTitle($this->asu_id, 'title') . " ({$this->getEnglishSubjectNameAttribute()})";
    }

    public function getEnglishSubjectNameAttribute()
    {
        $engTitle = $this->subject()->getEnglishTitle($this->asu_id, 'title_en');

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

    public function lecturers()
    {
        return $this->teachers()->where('type', self::LECTOR)->select('id', 'catalog_selective_subject_id', 'asu_id');
    }

    public function practice()
    {
        return $this->teachers()->where('type', self::PRACTICE)->select('id', 'catalog_selective_subject_id', 'asu_id');
    }

    public function lecturersSave($teachers)
    {
        $lectures = array_map(function ($teacher) {
            $teacher['type'] = self::LECTOR;
            return $teacher;
        }, $teachers);

        return $this->teachers()->createMany($lectures);
    }

    public function practiceSave($teachers)
    {
        $lectures = array_map(function ($teacher) {
            $teacher['type'] = self::PRACTICE;
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
    public function specializationCatalog()
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
        $query->where('published', 1);
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\CatalogSelectiveSubjectFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }

    protected static function booted()
    {
        static::saving(function ($catalog) {
            $catalog->user_id = 1; // Auth::id(); TODO: FIX IT
        });
    }
}
