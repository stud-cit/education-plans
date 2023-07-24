<?php

namespace App\Http\Resources;

use App\Models\Plan;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\HoursModules;
use App\Models\SemestersCredits;
use App\Http\Resources\VerificationPlanResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'guid' => $this->guid,
            'title' => $this->title,
            'faculty' => $this->facultyName,
            'faculty_id' => $this->faculty_id,
            'department_id' => $this->department_id,
            'department' => $this->departmentName,
            'study_term_id' => $this->study_term_id,
            'year' => $this->year,
            'form_study' => $this->formStudy,
            'form_organization' => $this->formOrganization,
            'education_level' => $this->educationLevel,
            'study_term' => $this->studyTerm,
            'form_study_id' => $this->formStudy ? $this->formStudy->id : null,
            'form_organization_id' => $this->formOrganization ? $this->formOrganization->id : null,
            'education_level_id' => $this->educationLevel ? $this->educationLevel->id : null,
            'credits' => $this->credits,
            'number_semesters' => $this->number_semesters,
            'speciality_id' => $this->speciality_id,
            'specialization_id' => $this->specialization_id,
            'education_program_id' => $this->education_program_id,
            'qualification_id' => $this->qualification_id,
            'field_knowledge_id' => $this->field_knowledge_id,
            'cycles' => CycleEditResource::collection($this->cycles->whereNull('cycle_id')),
            'hours_weeks_semesters' => $this->hours_weeks_semesters ?
                json_decode($this->hours_weeks_semesters) : null,
            'created_at' => $this->created_at,
            'verification' => VerificationPlanResource::collection($this->verification),
            'published' => $this->published,
            'schedule_education_process' => $this->schedule_education_process ?
                json_decode($this->schedule_education_process) : null,
            'signatures' => SignatureResource::collection($this->signatures),
            'program_op_id' => $this->program_op_id,
            'need_verification' => $this->need_verification,
            'summary_data_budget_time' => $this->summary_data_budget_time ?
                json_decode($this->summary_data_budget_time) : [],
            'practical_training' => $this->practical_training ?
                json_decode($this->practical_training) : [],
            'sum_semesters_credits' => $this->getSumSemestersCredits(),
            'sum_semesters_hours' => $this->getSumSemestersHours(),
            'count_exams' => $this->getCountExams(),
            'count_coursework' => $this->getCountCoursework(),
            'count_credits_selective_discipline' => $this->getCountCreditsSelectiveDiscipline(),
            'exams_table' => $this->getExamsTable($this->cycles),
            'short_plan' => $this->type_id === Plan::SHORT,

            'errors' => $this->setErrors(),
            'status_op' => $this->getStatusOP(),
            'comment' => $this->comment ? $this->comment : '',
            'not_conventional' => $this->not_conventional,
            'summary_data_budget_time' => $this->summary_data_budget_time ?
                json_decode($this->summary_data_budget_time) : [],
            'practical_training' => $this->practical_training ?
                json_decode($this->practical_training) : [],
            'need_verification' => $this->need_verification,
            'shorted_by_year' => $this->shortedByYear,
            'errors' => $this->setErrors(),
            'status_op' => $this->getStatusOP(),
            'comment' => $this->comment ? $this->comment : '',
            'not_conventional' => $this->not_conventional,
        ];
    }

    function setErrors(): array
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

    function checkCredit(): ?string
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

    function sumSemestersCreditsHasErrors()
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

    function hoursWeeksSemestersHasErrors()
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

    function semesterExamHasErrors()
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
            return "Перевищена кількість екзаменів у " . implode(', ', $result) . " семестрі.";
        }
    }

    function courseWorksHasErrors()
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

    function getOptions($key)
    {
        // TODO: set cache options;
        $options = Setting::select('id', 'key', 'value')->pluck('value', 'key');
        // TODO: KEY EXIST?
        return $options[$key];
    }

    function jsonDecodeToArray($json)
    {
        return $json ? json_decode($json, true) : null;
    }

    function getErrorsSemestersHours()
    {
        $result = [];
        foreach ($this->sum_semesters_hours as $index => $item) {
            if ($item > $this->sumArray(
                array_filter($this->hours_weeks_semesters, function ($i) use ($index) {
                    return $i['semester'] == $index + 1;
                }),
                'hour'
            )) {
                $result[] = $index + 1;
            }
        }
        return implode(', ', $result);
    }

    function sumArray($array, $field)
    {
        return array_reduce($array, function ($prev, $curr) use ($field) {
            return $prev + $curr[$field];
        }, 0);
    }

    function getSumSemestersHours()
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
    // нема такого метода
    function getSumSemestersCredits()
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

    // відрізняється
    // викликають загальний метод getCountWorks
    function getCountExams()
    {
        $result = [];

        for ($i = 0; $i < $this->studyTerm->semesters; $i++) {
            array_push($result, $this->getCountWorks(['form_control_id' => 1], $i + 1));
        }
        return $result;
    }
    // не відрізняються
    // викликають загальний метод getCountWorks
    function getCountCoursework()
    {
        $result = [];
        for ($i = 0; $i < $this->studyTerm->semesters; $i++) {
            array_push($result, $this->getCountWorks(['individual_task_id' => 2], $i + 1));
        }
        return $result;
    }
    // не відрізняється
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

    // нема такого метода
    function getCountCreditsSelectiveDiscipline()
    {
        $planId = $this->id;
        $count = Subject::with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
            $queryCycle->where('plan_id', $planId);
        })->sum('credits');
        return intval($count);
    }
}
