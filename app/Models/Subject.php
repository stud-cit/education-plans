<?php

namespace App\Models;

use App\ExternalServices\Asu\Subjects;
use App\Traits\HasAsuDivisionsNameTrait;
use App\Http\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;
    use HasAsuDivisionsNameTrait;

    public $timestamps = false;

    protected $fillable = [
        'asu_id',
        'cycle_id',
        'selective_discipline_id',
        'credits',
        'hours',
        'practices',
        'laboratories',
        'verification',
        'faculty_id',
        'department_id',
        'note',
        'subject_id'
    ];

    protected $appends = ['title'];

    protected $casts = [
        'asu_id' => 'integer'
    ];

    public function getGettingReadyLecturesAttribute(): float
    {
        return $this->hours * 0.25;
    }

    public function getGettingReadyPracticesAttribute(): float
    {
        return $this->practices * 0.25;
    }

    public function getGettingReadyLaboratoriesAttribute(): float
    {
        return $this->laboratories * 0.5;
    }

    public function getFinalFormAssessmentAttribute(): int
    {
        $formControl = $this->hoursModules->last(function ($value) {
            return in_array($value->form_control_id, [
                Constant::FORM_CONTROL['EXAM'],
                Constant::FORM_CONTROL['DIFFERENTIAL_TEST'],
            ]);
        });

        switch ($formControl?->form_control_id) {
            case Constant::FORM_CONTROL['EXAM']:
                return 30;
            case Constant::FORM_CONTROL['DIFFERENTIAL_TEST']:
                return 10;
            default:
                return 0;
        }
    }

    public function getExtraIndividualTasksAttribute(): int
    {
        $individualTasks = $this->hoursModules->filter(function ($item) {
            return in_array($item->individual_task_id, [
                Constant::INDIVIDUAL_TASKS['CONTROL_WORK'],
                Constant::INDIVIDUAL_TASKS['COURSE_WORK']
            ]);
        })->groupBy('individual_task_id');

        $courseWork = $individualTasks->has(Constant::INDIVIDUAL_TASKS['COURSE_WORK']);
        $controlWork = $individualTasks->has(Constant::INDIVIDUAL_TASKS['CONTROL_WORK']);

        if ($courseWork) {
            return count($individualTasks[Constant::INDIVIDUAL_TASKS['COURSE_WORK']]) * 30;
        } else if ($controlWork) {
            return count($individualTasks[Constant::INDIVIDUAL_TASKS['CONTROL_WORK']]) * 10;
        }

        return 0;
    }

    public function getIndependentWorkAttribute(): array
    {
        $allHours = $this->credits * 30;

        $classroomWork = $this->hours + $this->practices + $this->laboratories;

        $allIndependetWork = $this->gettingReadyLectures +
            $this->gettingReadyPractices +
            $this->gettingReadyLaboratories +
            $this->finalFormAssessment +
            $this->extraIndividualTasks;

        $result = $allHours - $classroomWork - $allIndependetWork;
        $rule = $allHours * 0.1;

        if ($result < $rule) {
            return [
                'subject_id' => $this->id,
                'error' => "Неправильно розподілено навчальне навантаження за дисципліною.",
                'title' => empty($this->selective_discipline_id) ? $this->title : $this->selectiveDiscipline->title,
            ];
        }

        return [];
    }

    public function selectiveDiscipline()
    {
        return $this->belongsTo(SelectiveDiscipline::class);
    }

    public function cycle()
    {
        return $this->belongsTo(Cycle::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'subject_id')->with([
            'subjects',
            'semestersCredits',
            'hoursModules.formControl',
            'exams',
            'test',
            'individualTasks',
            'hoursModules.individualTask'
        ]);
    }

    public function hoursModules()
    {
        return $this->hasMany(HoursModules::class);
    }

    public function exams()
    {
        return $this->getSemestersOnFormControl([Constant::FORM_CONTROL['EXAM']]);
    }

    public function test()
    {
        return $this->getSemestersOnFormControl([
            Constant::FORM_CONTROL['TEST'],
            Constant::FORM_CONTROL['DIFFERENTIAL_TEST']
        ]);
    }

    public function individualTasks()
    {
        return $this->getSemestersOnFormControl([Constant::FORM_CONTROL['PROTECTION']])
            ->orWhereIn('individual_task_id', [
                Constant::INDIVIDUAL_TASKS['CONTROL_WORK'],
                Constant::INDIVIDUAL_TASKS['COURSE_WORK']
            ]);
    }


    public function semestersCredits()
    {
        return $this->hasMany(SemestersCredits::class);
    }

    public function getTitleAttribute(): string
    {
        $subjects = new Subjects();
        return $subjects->getTitle($this->asu_id);
    }

    private function getSemestersOnFormControl(array $form_control_ids): HasMany
    {
        return $this->hoursModules()
            ->select(['subject_id'])
            ->selectRaw('(GROUP_CONCAT(DISTINCT semester)) as semester')
            ->whereIn('form_control_id', $form_control_ids)
            ->groupBy('subject_id');
    }

    public function notPartSpecialCycle(): bool
    {
        return !in_array($this->cycle->list_cycle_id, [Cycle::ATTESTATION, Cycle::PRACTICAL_TRAINING]);
    }

    public function subjectNotBelongAttestationCycle(): bool
    {
        return $this->cycle->list_cycle_id !== Cycle::ATTESTATION;
    }

    public function subjectNotBelongPracticalTraining(): bool
    {
        return $this->list_cycle_id !== Cycle::PRACTICAL_TRAINING;
    }

    public function getLastFormControlAttribute()
    {
        $title = '';

        $this->hoursModules->last(function ($item) use (&$title) {
            if ($item['form_control_id'] !== 10) {
                $title = $item->formControl->title;
                return $item;
            };
        });

        return $title;
    }

    public function checkCountCreditSubjects()
    {
        $subjectsId = $this->id;
        $count = Subject::with('subjects')->whereHas('subjects', function ($querySubjects) use ($subjectsId) {
            $querySubjects->where('subject_id', $subjectsId);
        })->sum('credits');
        return $count > $this->credits;
    }

    public static function boot()
    {
        parent::boot();
        self::deleting(function ($subject) { // before delete() method call this
            $subject->subjects()->each(function ($related) {
                $related->delete(); // direct deletion
            });
        });
    }
}
