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
            'cycles' => $this->cycles->whereNull('cycle_id')->toArray(),
            'hours_weeks_semesters' => $this->hours_weeks_semesters ?
                json_decode($this->hours_weeks_semesters) : null,
            'created_at' => $this->created_at,
            'verification' => $this->verification,
            'published' => $this->published,
            'schedule_education_process' => $this->schedule_education_process ?
                json_decode($this->schedule_education_process) : null,
            'sum_semesters_credits' => $this->getSumSemestersCredits(),
            'sum_semesters_hours' => $this->getSumSemestersHours(),
            'count_exams' => $this->getCountExams(),
            'count_coursework' => $this->getCountCoursework(),
            'count_credits_selective_discipline' => $this->getCountCreditsSelectiveDiscipline(),
            'signatures' => SignatureResource::collection($this->signatures),
            'program_op_id' => $this->program_op_id
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
      $result = [];
      for ($i = 0; $i < $this->studyTerm->semesters; $i++) { 
        if($this->form_organization_id == 1) {
          array_push($result, '');
        }
        array_push($result, $this->getCountWorks(['form_control_id' => 1], $i + 1));
      }
      return $result;
    }

    function getCountCoursework() {
      $result = [];
      for ($i=0; $i < $this->studyTerm->semesters; $i++) { 
        if($this->form_organization_id == 1) {
          array_push($result, '');
        }
        array_push($result, $this->getCountWorks(['individual_task_id' => 2], $i + 1));
      }
      return $result;
    }

    function getCountWorks($work, $semester) {
      $planId = $this->id;
      $count = HoursModules::with('subject')->whereHas('subject', function ($querySubject) use ($planId) {
        $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
          $queryCycle->where('plan_id', $planId);
        });
      })->where($work)->where('semester', $semester)->count();
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
