<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SemestersCredits;
use App\Models\HoursModules;
use App\Models\Subject;

class PlanShowResource extends JsonResource
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
            'specialization' => $this->specialization,
            'education_program_id' => $this->education_program_id,
            'qualification_id' => $this->qualification_id,
            'field_knowledge_id' => $this->field_knowledge_id,
            'cycles' => $this->cycles->whereNull('cycle_id')->toArray(),
            'max_hours_semesters' => $this->max_hours_semesters,
            // 'hours_week' => json_decode($this->hours_week),
            'hours_week' => $this->hours_week,
            'created_at' => $this->created_at,
            'schedule_education_process' => json_decode($this->schedule_education_process),
            'sum_semesters_credits' => $this->getSumSemestersCredits(),
            'count_exams' => $this->getCountExams(),
            'count_coursework' => $this->getCountCoursework(),
            'count_credits_selective_discipline' => $this->getCountCreditsSelectiveDiscipline(),
        ];
    }

    function getSumSemestersCredits() {
      $planId = $this->id;
      $result = [];
      $semestersWithCredits = SemestersCredits::select('semester', 'credit', 'subject_id')->with('subject')->whereHas('subject', function ($querySubject) use ($planId) {
        $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
          $queryCycle->where('plan_id', $planId);
        });
      })->get();
      foreach ($semestersWithCredits as $value) {
        if(isset($result[$value['semester']])) {
          $result[$value['semester']] += $value['credit'];
        } else {
          $result += [$value['semester'] => $value['credit']];
        }
      }
      return $result;
    }

    function getCountExams() {
      $planId = $this->id;
      $count = HoursModules::with('subject')->whereHas('subject', function ($querySubject) use ($planId) {
        $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
          $queryCycle->where('plan_id', $planId);
        });
      })->where('individual_task_id', 2)->count();
      return $count;
    }

    function getCountCoursework() {
      $planId = $this->id;
      $count = HoursModules::with('subject')->whereHas('subject', function ($querySubject) use ($planId) {
        $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
          $queryCycle->where('plan_id', $planId);
        });
      })->where('form_control_id', 1)->count();
      return $count;
    }

    function getCountCreditsSelectiveDiscipline() {
      $planId = $this->id;
      $count = Subject::with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
          $queryCycle->where('plan_id', $planId);
      })->sum('credits');
      return intval($count);
    }
}
