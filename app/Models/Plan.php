<?php

namespace App\Models;

use App\ExternalServices\Asu\Profession;
use App\ExternalServices\Asu\Qualification;
use App\Helpers\Filters\FilterBuilder;
use App\Traits\HasAsuDivisionsNameTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Plan extends Model
{
    use HasFactory;
    use HasAsuDivisionsNameTrait;
    use \Bkwld\Cloner\Cloneable;

    protected $fillable = [
        'guid',
        'title',
        'faculty_id',
        'department_id',
        'form_organization_id',
        'credits',
        'number_semesters',
        'qualification_id',
        'education_program_id',
        'field_knowledge_id',
        'year',
        'study_term_id',
        'hours_weeks_semesters',
        'schedule_education_process',
        'form_term_id',
        'education_level_id',
        'speciality_id',
        'specialization_id',
        'form_study_id',
        'published'
    ];

    protected $casts = [
        'year' => 'int',
        'speciality_id' => 'int',
        'count_hours' => 'int',
        'count_week' => 'int',
        'credits' => 'int',
        'number_semesters' => 'int',
        'qualification_id' => 'int',
        'education_program_id' => 'int',
        'field_knowledge_id' => 'int',
        'form_organization_id' => 'int',
    ];

    public function getStatus() {
      $data = array_column($this->verification->toArray(), 'status');
      if(count($this->filterStatus($data, 1)) == 4) {
        $result = 'success';
      } elseif (count($data) > 0 && count($this->filterStatus($data, 0)) == 0) {
        $result = 'warning';
      } elseif (count($data) > 0 && count($this->filterStatus($data, 0)) >= 0) {
        $result = 'error';
      } else {
        $result = '';
      }
      return $result;
    }

    private function filterStatus($data, $id) {
      return array_filter($data, function($val) use ($id) {
        return $val == $id;
      });
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d.m.Y H:i');
    }

    public function getSpecialityIdNameAttribute(): string
    {
        $professions = new Profession();
        return $professions->getTitle($this->speciality_id);
    }

    public function getQualificationIdNameAttribute(): string
    {
        $qualifications = new Qualification();
        return $qualifications->getTitle($this->qualification_id);
    }

    public function getSpecializationIdNameAttribute(): string
    {
        $professions = new Profession();
        return $professions->getTitle($this->specialization_id);
    }

    public function getFieldKnowledgeIdNameAttribute(): string
    {
        $professions = new Profession();
        return $professions->getTitle($this->field_knowledge_id);
    }

    public function getEducationProgramIdNameAttribute(): string
    {
        $professions = new Profession();
        return $professions->getTitle($this->education_program_id);
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

    public function studyTerm()
    {
        return $this->belongsTo(StudyTerm::class);
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\PlanFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }

    public function verification()
    {
        return $this->hasMany(PlanVerification::class);
    }
}
