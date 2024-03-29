<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Policies\PlanPolicy;
use App\Observers\PlanObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        'program_op_id',
        'summary_data_budget_time',
        'practical_training'
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

    public function getStatusAttribute()
    {
        $data = array_column($this->verification->toArray(), 'status');
        if (count($this->filterStatus($data, 1)) == 5) {
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

    private function filterStatus($data, $id)
    {
        return array_filter($data, function ($val) use ($id) {
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

    public function getExamsTable($cycles)
    {
        $_subjects = $cycles->where('list_cycle_id', 10)->first();

        if ($_subjects === null) return [];

        $subjects = [];
        $_subjects->subjects->map(function ($subject) use (&$subjects) {
            $semester = $subject->semestersCredits->filter(fn ($item) => $item->credit !== 0)->last();
            $subjects[] = [
                'title' => $subject->title,
                'semester' => $semester->semester ?? ''
            ];
        });

        return $subjects;
    }

    public function getNotesAttribute()
    {
        $notes = new \App\Http\Controllers\NoteController;

        return $notes->getNotes();
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

    public function scopeOfUserType($query, $type)
    {
        switch ($type) {
            case User::TRAINING_DEPARTMENT:
            case User::PRACTICE_DEPARTMENT:
            case User::EDUCATIONAL_DEPARTMENT_DEPUTY:
            case User::EDUCATIONAL_DEPARTMENT_CHIEF:
                return $query->whereHas('verification', function ($query) {
                    $query->where('verification_statuses_id', VerificationStatuses::OP)->where('status', true);
                });

            case User::FACULTY_INSTITUTE:
                return $query->whereNull(['parent_id', 'faculty_id'])
                    ->orWhere('faculty_id', '=', Auth::user()->faculty_id)
                    ->orWhereNull('faculty_id');

            case User::DEPARTMENT:
                return $query->whereNull(['parent_id', 'faculty_id', 'department_id'])
                    ->orWhere(function ($query) {
                        $query->where('faculty_id', '=', Auth::user()->faculty_id)->whereNull('department_id')
                            ->orWhere('department_id', '=', Auth::user()->department_id);
                    });

            default:
                return $query;
        }
    }

    public function scopePublished($query)
    {
        $query->where('published', 1);
    }

    public function isNotTemplate()
    {
        return $this->parent_id !== null ? true : false;
    }

    public function actions()
    {
        $policy = new PlanPolicy();
        $user = Auth::user();

        return [
            'preview' => $policy->viewAny($user),
            'copy' =>  Gate::allows('copy-plan'),
            'edit' => $policy->update($user, $this),
            'delete' => $policy->delete($user, $this),
        ];
    }

    protected static function booted()
    {
        static::creating(function ($plan) {
            $plan->author_id = Auth::id();
        });

        static::replicating(function ($plan) {
            $user = Auth::user();
            $plan->guid = Str::uuid();
            $plan->author_id = $user->id;

            if (!in_array($user->role_id, User::PRIVILEGED_ROLES)) {
                $plan->faculty_id = $user->faculty_id;
            }

            if ($user->role_id === User::DEPARTMENT) {
                $plan->department_id = $user->department_id;
            }
        });
        Plan::observe(PlanObserver::class);
    }
}
