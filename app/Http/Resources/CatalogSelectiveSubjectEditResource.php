<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogSelectiveSubjectEditResource extends JsonResource
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
            'asu_id' => $this->asu_id,
            'title' => $this->title,
            'title_en' => $this->title_en,
            'language' => $this->languages,
            'lecturers' => $this->lecturers,
            'practice' => $this->practice,
            'faculty_id' => $this->faculty_id,
            'department_id' => $this->department_id,
            'list_fields_knowledge' => $this->listFieldsKnowledge,
            'educationLevel' => $this->educationLevel,
            'general_competence' => $this->general_competence,
            'learning_outcomes' => $this->learning_outcomes,
            'entry_requirements_applicants' => $this->entry_requirements_applicants,
            'types_educational_activities' => $this->types_educational_activities,
            'number_acquirers' => $this->number_acquirers,
            'limitation' => json_decode($this->limitation),
        ];
    }
}
