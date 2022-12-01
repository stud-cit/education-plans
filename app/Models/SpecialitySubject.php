<?php

namespace App\Models;

use App\Models\Traits\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAsuDivisionsNameTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpecialitySubject extends Model
{
    use HasFactory, HasAsuDivisionsNameTrait, Subject;

    protected $table = 'catalog_selective_subjects';

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

    /**
     * Get second catalog Вибіркові дисципліни за спеціальністю
     */
    public function specialtiesCatalog()
    {
        return $this->catalog()->where('selective_discipline_id', 2);
    }

    // TODO: make policy
    public function actions()
    {
        // $policy = new CatalogSelectiveSubjectPolicy();
        // $user = Auth::user();

        // return [
        // 'preview' => $policy->viewAny($user),
        // 'edit' => $policy->update($user, $this),
        // 'delete' => $policy->delete($user, $this),
        // ];
    }

    protected static function booted()
    {
        static::creating(function ($catalog) {
            $catalog->user_id =  1; //Auth::id();
        });
    }
}
