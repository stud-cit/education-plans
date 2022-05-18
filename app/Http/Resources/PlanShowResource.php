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
            'department' => $this->departmentName,
            'year' => $this->year,
            'form_study' => $this->formStudy ? $this->formStudy->title : null,
            'form_organization' => $this->formOrganization ? $this->formOrganization->title : null,
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
