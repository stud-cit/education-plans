<?php

namespace App\Models;

use App\ExternalServices\ASU;
use App\Helpers\Filters\FilterBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'faculty_id',
        'department_id',
        'year'
    ];

    protected $casts = [
        // 'created_at' => 'datetime:Y-m-d',
        'year' => 'int',
        'specialization_id' => 'int',
        'count_hours' => 'int',
        'count_week' => 'int',
        'credits' => 'int',
        'number_semesters' => 'int',
        'qualification_id' => 'int',
        'education_program_id' => 'int',
        'field_knowledge_id' => 'int'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d.m.Y H:m');
    }

    public function getFacultyAttribute()
    {
        $asu = new ASU();
        return $asu->getNameFacultyById($this->faculty_id);
    }

    public function getDepartmentAttribute()
    {
        $asu = new ASU();
        return $asu->getNameDepartmentById($this->department_id);
    }

    public function formStudy()
    {
        return $this->belongsTo(FormStudy::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\PlanFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);
        
        return $filter->apply(); 
    }
}
