<?php

namespace App\Http\Resources\Api;

use App\Helpers\Helpers;
use App\Models\Plan;
use App\Models\Subject;
use App\Models\HoursModules;
use App\Http\Resources\CycleShowResource;
use App\Http\Resources\SignatureShowResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationPlanShowResource extends JsonResource
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
            'department' => $this->departmentName,
            'department_id' => $this->department_id,
            'year' => $this->year,
            'form_study' => $this->formStudy,
            'form_organization' => $this->formOrganization,
            'education_level' => $this->educationLevel,
            'study_term' => $this->studyTerm,
            'form_organization_id' => $this->formOrganization ? $this->formOrganization->id : null,
            'credits' => $this->credits,
            'number_semesters' => $this->number_semesters,
            'speciality' => $this->speciality_id_name,
            'speciality_id' => $this->speciality_id,
            'specialization' => $this->specialization_id_name,
            'specialization_id' => $this->specialization_id,
            'education_program' => $this->education_program_id_name,
            'education_program_id' => $this->education_program_id,
            'qualification' => $this->qualification_id_name,
            'qualification_id' => $this->qualification_id,
            'field_knowledge' => $this->field_knowledge_id_name,
            'field_knowledge_id' => $this->field_knowledge_id,
            'cycles' => CycleShowResource::collection($this->cycles->whereNull('cycle_id')),
            'hours_weeks_semesters' => $this->hours_weeks_semesters ?
                json_decode($this->hours_weeks_semesters) : null,
            'schedule_education_process' => $this->schedule_education_process ?
                json_decode($this->schedule_education_process) : null,
            'signatures' => SignatureShowResource::collection($this->signatures),
            'count_exams' => $this->getCountExams(),
            'count_tests' => $this->getCountTests(),
            'count_coursework' => $this->getCountCoursework(),
            'notes' => $this->notes,
            'exams_table' => $this->getExamsTable($this->cycles),
            'individual_task_semester' => $this->getIndividualTaskSemester($this->cycles),
            'summary_data_budget_time' => $this->summary_data_budget_time ?
                json_decode($this->summary_data_budget_time) : [],
            'practical_training' => $this->practical_training ?
                json_decode($this->practical_training) : [],
            'subject_notes' => $this->getSubjectNotes(),
        ];
    }
}
