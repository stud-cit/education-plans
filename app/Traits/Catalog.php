<?php

namespace App\Traits;

use App\Models\CatalogEducationLevel;
use App\Helpers\Filters\FilterBuilder;
use App\Models\CatalogSelectiveSubject;
use App\ExternalServices\Asu\Profession;

trait Catalog
{
    public function getEducationProgramIdNameAttribute()
    {
        if (!$this->education_program_id) return null;

        $professions = new Profession();
        return $professions->getTitle($this->education_program_id, 'title');
    }

    public function getSpecialityIdNameAttribute()
    {
        if (!$this->speciality_id) return null;

        $professions = new Profession();

        $code = $professions->getTitle($this->speciality_id, 'code');
        return "{$code} {$professions->getTitle($this->speciality_id, 'title')}";
    }

    public function educationLevel()
    {
        return $this->belongsTo(CatalogEducationLevel::class, 'catalog_education_level_id');
    }

    public function subjects()
    {
        return $this->hasMany(CatalogSelectiveSubject::class, 'catalog_subject_id');
    }
}
