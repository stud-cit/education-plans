<?php

namespace App\Http\Resources;

use App\Helpers\Tree;
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
        // $cycles = Tree::makeTree($this->cycles);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'faculty' => $this->facultyName,
            'faculty_id' => $this->faculty_id,
            'department_id' => $this->department_id,
            'department' => $this->departmentName,
            'term_study_id' => $this->study_term_id,
            'year' => $this->year,
            'form_study_id' => $this->formStudy ? $this->formStudy->id : null,
            'form_study' => $this->formStudy ? $this->formStudy->title : null,
            'form_organization_id' => $this->formOrganization ? $this->formOrganization->id : null,
            'form_organization' => $this->formOrganization ? $this->formOrganization->title : null,
            'education_level_id' => $this->educationLevel ? $this->educationLevel->id : null,
            'education_level' => $this->educationLevel ? $this->educationLevel->title : null,
            'credits' => $this->credits,
            'number_semesters' => $this->number_semesters,
            'speciality_id' => $this->speciality_id,
            'specialization' => $this->specialization,
            'education_program_id' => $this->education_program_id,
            'qualification_id' => $this->qualification_id,
            'field_knowledge_id' => $this->field_knowledge_id,
            'cycles' => $this->cycles->whereNull('cycle_id'),
            'count_hours' => $this->count_hours,
            'count_week' => $this->count_week,
            'created_at' => $this->created_at,
        ];
    }
}
