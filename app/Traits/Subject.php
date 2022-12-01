<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Teacher;
use App\Models\CatalogSubject;
use App\Models\LanguageSubject;
use App\ExternalServices\Asu\Subjects;
use App\Helpers\Filters\FilterBuilder;

trait Subject
{
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
        return $this->hasMany(Teacher::class, 'catalog_selective_subject_id');
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

    public function scopePublished($query)
    {
        return $query->where('published', 1);
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
}
