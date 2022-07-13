<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Model;
use App\ExternalServices\Asu\Profession;
use App\Traits\HasAsuDivisionsNameTrait;
use App\ExternalServices\Asu\Qualification;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Plan extends Model
{
    use HasFactory;
    use HasAsuDivisionsNameTrait;
    use \Bkwld\Cloner\Cloneable;

    protected $cloneable_relations = ['signatures'];

    protected $fillable = [
        'guid',
        'parent_id',
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
        'published',
        'program_op_id'
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

    public function getSpecialityIdNameAttribute()
    {
        if (!$this->speciality_id) return null;

        $professions = new Profession();

        $code = $professions->getTitle($this->speciality_id, 'code');
        return "{$code} {$professions->getTitle($this->speciality_id, 'title')}";
    }

    public function getQualificationIdNameAttribute(): string
    {
        $qualifications = new Qualification();
        return $qualifications->getTitle($this->qualification_id);
    }

    public function getSpecializationIdNameAttribute()
    {
        if (!$this->specialization_id) return null;

        $professions = new Profession();
        return $professions->getTitle($this->specialization_id, 'title');
    }

    public function getFieldKnowledgeIdNameAttribute()
    {
        if (!$this->field_knowledge_id) return null;

        $professions = new Profession();

        $code = $professions->getTitle($this->field_knowledge_id, 'code');
        return "{$code} {$professions->getTitle($this->field_knowledge_id, 'title')}";
    }

    public function getEducationProgramIdNameAttribute()
    {
        if (!$this->education_program_id) return null;

        $professions = new Profession();
        return $professions->getTitle($this->education_program_id, 'title');
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

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    protected static function booted()
    {
        static::creating(function ($plan) {
            $plan->author_id = Auth::id();
        });

        static::replicating(function ($plan) {
            $plan->author_id = Auth::id();
        });
    }
}
