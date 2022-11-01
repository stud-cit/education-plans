<?php

namespace App\Models;

use App\Models\User;
use App\Models\CatalogSubject;
use App\Models\CatalogEducationLevel;
use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAsuDivisionsNameTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogSelectiveSubject extends Model
{
    use HasFactory, HasAsuDivisionsNameTrait;

    protected $fillable = [
        'asu_id',
        'faculty_id',
        'department_id',
        'catalog_education_level_id',
        'user_id',
        'title',
        'title_en',
        'list_fields_knowledge',
        'types_educational_activities',
        'general_competence',
        'number_acquirers',
        'entry_requirements_applicants',
        'limitation',
        'published'
    ];

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

    /**
     * Get first catalog Вибіркові дисципліни (каталог)
     *
     * @return void
     */
    public function selectiveCatalog()
    {
        return $this->catalog()->where('selective_discipline_id', 1);
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\CatalogSelectiveSubjectFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }
}
