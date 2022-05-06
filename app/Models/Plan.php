<?php

namespace App\Models;

use App\Helpers\Filters\FilterBuilder;
use App\Traits\HasAsuDivisionsNameTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    use HasAsuDivisionsNameTrait;

    protected $fillable = [
        'title',
        'faculty_id',
        'department_id',
        'form_organization_id',
        'credits',
        'number_semesters',
        'qualification_id',
        'education_program_id',
        'field_knowledge_id',
        'count_week',
        'count_hours',
        'year'
    ];

    protected $casts = [
        'year' => 'int',
        'specialization_id' => 'int',
        'count_hours' => 'int',
        'count_week' => 'int',
        'credits' => 'int',
        'number_semesters' => 'int',
        'qualification_id' => 'int',
        'education_program_id' => 'int',
        'field_knowledge_id' => 'int',
        'form_organization_id' => 'int',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d.m.Y H:i');
    }

    public function formStudy()
    {
        return $this->belongsTo(FormStudy::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function cycles()
    {
        return $this->hasMany(Cycle::class)->with('subjects');
    }

    public function formOrganization()
    {
        return $this->belongsTo(FormOrganization::class);
    }

    // public function studyTerm()
    // {
    //     return $this->belongsTo(StudyTerm::class);
    // }

    public function replicateRow()
    {
        $clone = $this->replicate();
        $clone->created_at = now();
        $clone->push();

        foreach($this->cycles as $cycle)
        {
            $clone->cycles()->create($cycle->toArray());
        }

        $clone->save();
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\PlanFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }
}
