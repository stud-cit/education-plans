<?php

namespace App\Http\Resources;

use App\Models\Plan;
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
            'title' => $this->planTitle,
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
            'comment' => $this->comment ? $this->comment : '',
            'not_conventional' => $this->not_conventional,
            'shorted_by_year' => $this->shortedByYear,
            'basePlan' => $this->basePlanData,
            'approvedPlan' => $this->approvedPlan,
            'actions' => [
                'can_generate_short_plan' => $this->canGenerateShortPlan(),
            ],
            'verification_comments' => $this->verification_comments,
            'type_id' => $this->type_id,
            'duplicate_message' => $this->duplicate_message
        ];
    }
}
