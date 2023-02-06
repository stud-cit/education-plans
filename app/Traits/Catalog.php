<?php

namespace App\Traits;

use App\Models\EducationLevel;
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
        return $this->belongsTo(EducationLevel::class, 'catalog_education_level_id')->withTrashed();
    }

    public function subjects()
    {
        return $this->hasMany(CatalogSelectiveSubject::class, 'catalog_subject_id');
    }

    private function years(): String
    {
        $nextYear = $this->year + 1;
        return  "{$this->year}-{$nextYear}р.";
    }
}
