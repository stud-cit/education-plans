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
            'faculty' => $this->faculty,
            'department' => $this->department,
            'year' => $this->year,
            'form_study' => $this->formStudy->title,
            'education_level' => $this->educationLevel->title,
            'credits' => $this->credits,
            'number_semesters' => $this->number_semesters,
            'specialization_id' => $this->specialization_id,
            'specialization' => $this->specialization,
            'education_program_id' => $this->education_program_id,
            'qualification_id' => $this->qualification_id,
            'field_knowledge_id' => $this->field_knowledge_id,
            'count_hours' => $this->count_hours,
            'count_week' => $this->count_week,
            'created_at' => $this->created_at,
        ];
    }
}
