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
        'practical_training',
        'comment',
        'not_conventional',
        'need_verification'
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
        'published' => 'boolean',
        'not_conventional' => 'boolean',
        'need_verification' => 'boolean',
    ];

    const TEMPLATE = 1;
    const PLAN = 2;
    const SHORT = 3;

    const SHORTED_BY_YEAR = [
        ['year' => 1, 'show' => false, 'label' => 'Згенерувати навчальний план, скорочений на 1 рік'],
        ['year' => 2, 'show' => false, 'label' => 'Згенерувати навчальний план, скорочений на 2 роки'],
    ];

    public function getStatusAttribute()
    {
        $accept = VerificationStatuses::fullPlanVerification();

        $data = array_column($this->verification->toArray(), 'status');
        if (count($this->filterStatus($data, 1)) >= $accept) {
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


    public function getShortedByYearAttribute()
    {
        if (!$this->isApprovedPlan()) return [];
        if ($this->isNotPlan()) return [];
        if ($this->form_study_id !== 1) return []; // денна форма навчання


        $terms = self::SHORTED_BY_YEAR;
        $termStudy = $this->studyTerm;
        $year = $termStudy['year'];
        $month = $termStudy['month'];

        foreach ($terms as &$value) {
            $value['show'] = $year >= 3 && $month >= 10;
            //value['id'] value['title'] // TODO: якщо скорочений план існує записати id
            //value['label'] Навчальний план, скорочений на 2 роки // TODO: Изменить название кнопки
        }

        return $terms;
    }

    protected function isApprovedPlan(): bool
    {
        $data = array_column($this->verification->toArray(), 'status');

        return count($this->filterStatus($data, 1)) >= 4;
    }

    public function getUserVerificationsAttribute()
    {
        return $this->verification;
    }
    
    public function getStatusOP()
    {
        return $this->verification()->select('id', 'verification_statuses_id', 'status')
            ->where('verification_statuses_id', 1)->value('status');
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

    public function getQualificationIdNameAttribute()
    {
        if (!$this->qualification_id) return null;

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
        return $professions->getTitle($this->education_program_id, 'title', true, ['label' => 'after']);
    }

    public function formStudy()
    {
        return $this->belongsTo(FormStudy::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class)->withTrashed();
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
            $semester = $subject->hoursModules->filter(fn ($item) => $item->form_control_id != 10 || $item->individual_task_id != 3)->last();
            $subjects[] = [
                'title' => $subject->title,
                'semester' => $semester->semester ?? ''
            ];
        });

        return $subjects;
    }

    public function getIndividualTaskSemester($cycles)
    {
        $_subjects = $cycles->where('list_cycle_id', 10)->first();

        if ($_subjects === null) return [];

        $subjects = [];
        $_subjects->subjects->map(function ($subject) use (&$subjects) {
            $semester = $subject->hoursModules->filter(fn ($item) => $item->form_control_id != 1 && ($item->form_control_id != 10 || $item->individual_task_id != 3))->last();
            $subjects[] = $semester->semester ?? '';
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

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    public function type()
    {
        return $this->hasOne(PlanType::class, 'id', 'type_id');
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
        return $this->type_id !== self::TEMPLATE ? true : false;
    }

    public function isNotPlan(): bool
    {
        return $this->type_id !== self::PLAN;
    }

    public function isNotShort(): bool
    {
        return $this->type_id !== self::SHORT ? true : false;
    }

    public function actions()
    {
        $policy = new PlanPolicy();
        $user = Auth::user();

        return [
            'preview' => $policy->viewAny($user),
            'copy' =>  Gate::allows('copy-plan', $this),
            'edit' => $policy->update($user, $this),
            'delete' => $policy->delete($user, $this),
        ];
    }

    public function getCountExams()
    {
        $result = [];
        for ($i = 0; $i < $this->studyTerm->semesters; $i++) {
            if ($this->form_organization_id == 1) {
                array_push($result, '');
            }
            array_push($result, $this->getCountWorks(['form_control_id' => 1], $i + 1));
        }
        return $result;
    }

    function getCountTests()
    {
        $result = [];
        for ($i = 0; $i < $this->studyTerm->semesters; $i++) {
            if ($this->form_organization_id == 1) {
                array_push($result, '');
            }
            array_push($result, $this->getCountWorks(['form_control_id' => 3, 'form_control_id' => 2], $i + 1));
        }
        return $result;
    }

    function getCountCoursework()
    {
        $result = [];
        for ($i = 0; $i < $this->studyTerm->semesters; $i++) {
            if ($this->form_organization_id == 1) {
                array_push($result, '');
            }
            array_push($result, $this->getCountWorks(['individual_task_id' => 2], $i + 1));
        }
        return $result;
    }

    function getSubjectNotes()
    {
        $planId = $this->id;
        $result = Subject::with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
            $queryCycle->where('plan_id', $planId);
        })->select('note', 'asu_id')->whereNotNull('note')->get();
        return $result;
    }

    function getCountWorks($work, $semester)
    {
        $planId = $this->id;
        $count = HoursModules::with('subject')->whereHas('subject', function ($querySubject) use ($planId) {
            $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
                $queryCycle->where('plan_id', $planId);
            });
        })->where($work)->where('semester', $semester)->count();
        return $count;
    }

    public function generateTitle(): string
    {
        $title = '';
        $professions = new Profession();
        $words = [
            ['id' =>  $this['speciality_id'], 'labels' => ['code', 'title'], 'type' => 'profession'],
            ['id' =>  $this['education_program_id'], 'labels' => 'title', 'type' => 'profession'],
            ['id' =>  $this['education_level_id'], 'labels' => 'title', 'model' => '\App\Models\EducationLevel', 'type' => 'model'],
            ['id' =>  0, 'labels' => 'year',  'type' => 'request'],
        ];

        foreach ($words as $word) {
            switch ($word['type']) {
                case 'profession':
                    if (!is_null($word['id'])) {
                        $title .= $professions->getTitleProfession($word['id'], $word['labels']) . " ";
                    }
                    break;
                case 'model':
                    $t = $word['model']::find($word['id']);
                    $title .= $t[$word['labels']] . " ";
                    break;
                case 'request':
                    $title .= $this[$word['labels']] . " ";
                    break;

                default;
            }
        }
        return trim($title);
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
