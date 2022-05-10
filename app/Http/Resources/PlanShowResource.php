<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'department' => $this->departmentName,
            'department_id' => $this->department_id,
            'year' => $this->year,
            'form_study' => $this->formStudy ? $this->formStudy->title : null,
            'form_organization' => $this->formOrganization,
            'education_level' => $this->educationLevel ? $this->educationLevel->title : null,
            'study_term' => $this->studyTerm,
            'credits' => $this->credits,
            'number_semesters' => $this->number_semesters,
            'specialization_id' => $this->specialization_id,
            'specialization' => $this->specialization,
            'education_program_id' => $this->education_program_id,
            'qualification_id' => $this->qualification_id,
            'field_knowledge_id' => $this->field_knowledge_id,
            'cycles' => $this->cycles->whereNull('cycle_id')->toArray(),
            'count_hours' => $this->count_hours,
            'count_week' => $this->count_week,
            'hours_week' => json_decode($this->hours_week),
            'created_at' => $this->created_at,
        ];
    }
}
