<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SemestersCredits;
use App\Models\HoursModules;
use App\Models\Subject;

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
            'department' => $this->departmentName,
            'year' => $this->year,
            'form_study' => $this->formStudy,
            'form_organization' => $this->formOrganization,
            'education_level' => $this->educationLevel,
            'study_term' => $this->studyTerm,
            'form_organization_id' => $this->formOrganization ? $this->formOrganization->id : null,
            'credits' => $this->credits,
            'number_semesters' => $this->number_semesters,
            'speciality' => $this->speciality_id_name,
            'specialization' => $this->specialization_id_name,
            'education_program' => $this->education_program_id_name,
            'qualification' => $this->qualification_id_name,
            'field_knowledge' => $this->field_knowledge_id_name,
            'cycles' => $this->cycles->whereNull('cycle_id')->toArray(),
            'hours_weeks_semesters' => json_decode($this->hours_weeks_semesters),
            'schedule_education_process' => json_decode($this->schedule_education_process),
//            'sum_semesters_credits' => $this->getSumSemestersCredits(),
//            'sum_semesters_hours' => $this->getSumSemestersHours(),
//            'count_exams' => $this->getCountExams(),
//            'count_coursework' => $this->getCountCoursework(),
//            'count_credits_selective_discipline' => $this->getCountCreditsSelectiveDiscipline(),
        ];
    }

    function getSumSemestersHours() {
      $planId = $this->id;
      $result = [];
      $semestersWithHours = HoursModules::select('semester', 'hour', 'subject_id')->with('subject')->whereHas('subject', function ($querySubject) use ($planId) {
        $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
          $queryCycle->where('plan_id', $planId);
        });
      })->get();
      foreach ($semestersWithHours as $value) {
        if(isset($result[$value['semester']])) {
          $result[$value['semester']] += $value['hour'];
        } else {
          $result += [$value['semester'] => $value['hour']];
        }
      }
      return $result;
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
