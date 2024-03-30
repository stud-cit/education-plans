<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Subject;
use Illuminate\Support\Str;
use App\Models\HoursModules;
use App\Policies\PlanPolicy;
use App\Models\ShortenedPlan;
use App\Observers\PlanObserver;
use App\Models\SemestersCredits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Model;
use App\ExternalServices\Asu\Profession;
use App\Traits\HasAsuDivisionsNameTrait;
use Illuminate\Database\Eloquent\Builder;
use App\ExternalServices\Asu\Qualification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Plan extends Model
{
    use HasFactory;
    use HasAsuDivisionsNameTrait;
    use \Bkwld\Cloner\Cloneable;
    use SoftDeletes;

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
        'need_verification',
        'type_id'
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
        $result = '';
        $accept = VerificationStatuses::fullPlanVerification();
        $data = array_column($this->verification->toArray(), 'status');
        $hasVerification = count($data) > 0;

        if (!$hasVerification) return $result;

        if ($this->countStatuses($data, 1) >= $accept) {
            $result = 'success';
        } elseif ($this->countStatuses($data, 0) == 0) {
            $result = 'warning';
        } elseif ($this->countStatuses($data, 0) >= 0) {
            $result = 'error';
        }

        return $result;
    }

    /**
     *
     * @param array $data
     * @param integer $status
     * @return integer
     */
    private function countStatuses(array $data, int $status): int
    {
        $filtered = array_filter($data, function ($val) use ($status) {
            return $val == $status;
        });

        return count($filtered);
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

        $shortenedPlan = $this->shortedPlan->keyBy('shortened_by_year');

        foreach ($terms as &$value) {
            $value['show'] = $year >= 3 && $month >= 10;
            $y = $value['year'];
            if (isset($shortenedPlan[$y])) {
                $btnName = trans_choice('variables.shortened_plan', $y, ['value' => $y]);

                $value['id'] =  $shortenedPlan[$y]['plan_id'];
                $value['title'] = "$btnName | $this->title";
                $value['label'] = $btnName;
            }
        }

        return $terms;
    }

    protected function isApprovedPlan(): bool
    {
        return $this->verification->sum('status') >= PlanVerification::FULL_VERIFICATION;
    }

    public function getApprovedPlanAttribute(): bool
    {
        return $this->verification->sum('status') >= PlanVerification::FULL_VERIFICATION;
    }

    public function getUserVerificationsAttribute()
    {
        return $this->verification;
    }

    public function getBasePlanDataAttribute()
    {
        $relation = $this->basePlan;

        if ($relation->isEmpty()) return null;

        $base_id = $relation->firstWhere('plan_id', $this->id)->parent_id;

        $title = Plan::select('title')->where('id', $base_id)->value('title');

        return ['base_id' => $base_id, 'title' => $title];
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

    public function getEducationProgramIdNameWithTypeAttribute(): array
    {
        if (!$this->education_program_id) return null;

        $professions = new Profession();
        return [
            $professions->getTitle($this->education_program_id, 'title', false),
            $professions->getTitle($this->education_program_id, 'label', false)
        ];
    }

    public function shortedPlan()
    {
        return $this->hasMany(ShortenedPlan::class, 'parent_id');
    }

    public function basePlan()
    {
        return $this->hasMany(ShortenedPlan::class, 'plan_id');
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
        return $this->hasMany(Cycle::class)->with('subjects.subjects');
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
        return $this->hasMany(PlanVerification::class)->where('verification_statuses_id', '!=', 1);
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
                return $query->where('need_verification', true);

            case User::FACULTY_INSTITUTE:
                return $query->whereNull(['faculty_id'])
                    ->orWhere('faculty_id', '=', Auth::user()->faculty_id)
                    ->orWhereNull('faculty_id');

            case User::DEPARTMENT:
                return $query->whereNull(['faculty_id', 'department_id'])
                    ->whereType(self::TEMPLATE)->verified()
                    ->orWhere(function ($query) {
                        $query->myFaculty()->verified()
                            ->whereNull('department_id')
                            ->orWhere('department_id', '=', Auth::user()->department_id);
                    });
            case User::GUEST:
                return $query->where('type_id', '!=', self::TEMPLATE)->whereHas('verification', function (Builder $query) {
                    $query->where('status', true);
                }, '>=', PlanVerification::FULL_VERIFICATION);
            default:
                return $query;
        }
    }

    public function scopeVerified($query)
    {
        $query->whereHas('verification', function (Builder $query) {
            $query->where('status', true);
        }, '>=', 5); // PlanVerification::FULL_VERIFICATION
    }

    public function scopeMyFaculty($query)
    {
        $query->where('faculty_id', '=', Auth::user()->faculty_id);
    }

    public function scopePublished($query)
    {
        $query->where('published', 1);
    }

    public function scopePlan($query)
    {
        $query->where('type_id', self::PLAN);
    }

    public function scopeWhereType($query, $type)
    {
        $query->where('type_id', $type);
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
    // !old
    // function getCountCoursework()
    // {
    //     $result = [];
    //     for ($i = 0; $i < $this->studyTerm->semesters; $i++) {
    //         if ($this->form_organization_id == 1) {
    //             array_push($result, '');
    //         }
    //         array_push($result, $this->getCountWorks(['individual_task_id' => 2], $i + 1));
    //     }
    //     return $result;
    // }

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

    public function isHasErrors(): bool
    {
        return (bool) count($this->setErrors());
    }

    public function setErrors(): array
    {
        $messages = [
            $this->sumSemestersCreditsHasErrors(),
            $this->hoursWeeksSemestersHasErrors(),
            $this->semesterExamHasErrors(),
            $this->courseWorksHasErrors(),
            $this->checkCredit(),
        ];

        return array_filter($messages);
    }

    public function sumSemestersCreditsHasErrors()
    {
        $result = [];
        $quantityCreditsSemester = $this->getOptions('quantity-credits-semester');

        foreach ($this->getSumSemestersCredits() as $index => $value) {
            if ($value > $quantityCreditsSemester) {
                $result[] = $index;
            }
        }

        if (empty($result)) {
            return null;
        } else {
            return "Перевищена кількість кредитів у " . implode(', ', $result) . " семестрі.";
        }
    }

    public function hoursWeeksSemestersHasErrors()
    {
        $result = [];

        $hoursWeeksSemesters = $this->jsonDecodeToArray($this->hours_weeks_semesters);
        if (!$hoursWeeksSemesters) {
            return null;
        }

        $resetSumSemesterHours = array_values($this->getSumSemestersHours());

        foreach ($resetSumSemesterHours as $index => $item) {
            if (isset($hoursWeeksSemesters[$index])) {
                if ($item > $hoursWeeksSemesters[$index]['hour']) {
                    $newIndx = $index;
                    $result[] = $newIndx + 1;
                }
            }
        }

        if (empty($result)) {
            return null;
        } else {
            return "Перевищена кількість годин у " . implode(', ', $result) . ($this->form_organization_id == 3 ? " семестрі." : " модулі.");
        }
    }

    public function semesterExamHasErrors()
    {
        $result = [];
        $numberExams = $this->getOptions('exam');

        foreach ($this->getCountExams() as $index => $value) {
            if ($value > $numberExams) {
                $result[] = $index + 1;
            }
        }

        if (empty($result)) {
            return null;
        } else {
            return "Перевищена кількість екзаменів у " . implode(', ', $result) . ($this->form_organization_id == 3 ? " семестрі." : " модулі.");
        }
    }

    public function courseWorksHasErrors()
    {
        $result = [];
        $numberExams = $this->getOptions('coursework');

        foreach ($this->getCountCoursework() as $index => $value) {
            if ($value > $numberExams) {
                $result[] = $index + 1;
            }
        }

        if (empty($result)) {
            return null;
        } else {
            return "Перевищена кількість курсових робіт у " . implode(', ', $result) . " семестрі.";
        }
    }

    public function checkCredit(): ?string
    {
        $planId = $this->id;

        $sum = Subject::select('credits')->with('cycle')
            ->whereHas('cycle', function ($queryCycle) use ($planId) {
                $queryCycle->where('plan_id', $planId);
            })->sum('credits');

        if ($sum > $this->credits) {
            return "Перевищена загальна кількість кредитів: $sum із $this->credits";
        }

        return null;
    }

    public function getCountCreditsSelectiveDiscipline()
    {
        $planId = $this->id;
        $count = Subject::with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
            $queryCycle->where('plan_id', $planId);
        })->sum('credits');
        return intval($count);
    }

    public function getSumSemestersCredits()
    {
        $planId = $this->id;
        $result = [];
        $semestersWithCredits = SemestersCredits::select('semester', 'credit', 'subject_id')->with('subject')->whereHas('subject', function ($querySubject) use ($planId) {
            $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
                $queryCycle->where('plan_id', $planId);
            });
        })->get();
        foreach ($semestersWithCredits as $value) {
            if (isset($result[$value['semester']])) {
                $result[$value['semester']] += $value['credit'];
            } else {
                $result += [$value['semester'] => $value['credit']];
            }
        }
        return $result;
    }

    public function getSumSemestersHours()
    {
        $planId = $this->id;
        $result = [];

        $semestersWithHours = HoursModules::select('id', 'module', 'hour')->with('subject.id')->whereHas('subject', function ($querySubject) use ($planId) {
            $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
                $queryCycle->where('plan_id', $planId);
            });
        })->get();

        foreach ($semestersWithHours as $value) {
            if (isset($result[$value['module']])) {
                $result[$value['module']] += $value['hour'];
            } else {
                $result += [$value['module'] => $value['hour']];
            }
        }
        return $result;
    }

    public function getCountCoursework()
    {
        $result = [];
        for ($i = 0; $i < $this->studyTerm->semesters; $i++) {
            array_push($result, $this->getCountWorks(['individual_task_id' => 2], $i + 1));
        }
        return $result;
    }

    private function getOptions($key)
    {
        // TODO: set cache options;
        $options = Setting::select('id', 'key', 'value')->pluck('value', 'key');
        // TODO: KEY EXIST?
        return $options[$key];
    }

    private function jsonDecodeToArray($string)
    {
        if (gettype($string) === 'string') {
            return $string ? json_decode($string, true) : null;
        }

        return $string;
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
