<?php

namespace App\Traits;

use App\Models\CatalogEducationLevel;
use App\Helpers\Filters\FilterBuilder;
use App\Models\CatalogSelectiveSubject;

trait Catalog
{
    public function educationLevel()
    {
        return $this->belongsTo(CatalogEducationLevel::class, 'catalog_education_level_id');
    }

    public function subjects()
    {
        return $this->hasMany(CatalogSelectiveSubject::class, 'catalog_subject_id');
    }
}
