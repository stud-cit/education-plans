<?php

namespace App\Models;

use App\Traits\Subject;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAsuDivisionsNameTrait;
use App\Policies\SpecialitySubjectPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpecialitySubject extends Model
{
    use HasFactory, HasAsuDivisionsNameTrait, Subject, \Bkwld\Cloner\Cloneable;

    protected $table = 'catalog_selective_subjects';

    protected $cloneable_relations = ['languages', 'teachers'];

    protected $fillable = [
        'id',
        'asu_id',
        'catalog_subject_id',
        'faculty_id',
        'department_id',
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
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public function onCloning($src, $child = null)
    {
        $src->load(['languages', 'teachers']);
    }
    /**
     * Get second catalog Вибіркові дисципліни за спеціальністю
     */
    public function specialtiesCatalog()
    {
        return $this->catalog()->where('selective_discipline_id', 2);
    }

    public function actions()
    {
        $policy = new SpecialitySubjectPolicy();
        $user = Auth::user();

        return [
            'preview' => $policy->viewAny($user),
            'edit' => $policy->update($user, $this),
            'delete' => $policy->delete($user, $this),
        ];
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\CatalogSpecialityFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }

    protected static function booted()
    {
        static::creating(function ($catalog) {
            $catalog->user_id =  1; //Auth::id();
        });
    }
}
