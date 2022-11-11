<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SubjectLanguage\SubjectLanguageListShowResource;

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
            'catalog' => $this->group_id,
            'id' => $this->id,
            'discipline' => [
                'id' => $this->asu_id,
                'title_en' => $this->title_en,
            ],
            'language' => SubjectLanguageListShowResource::collection($this->languages),
            'lecturers' => $this->lecturers,
            'practice' => $this->practice,
            'faculty_id' => $this->faculty_id,
            'department_id' => $this->department_id,
            'list_fields_knowledge' => json_decode($this->list_fields_knowledge),
            'education_level' => $this->educationLevel->id,
            'general_competence' => $this->general_competence,
            'learning_outcomes' => $this->learning_outcomes,
            'entry_requirements_applicants' => $this->entry_requirements_applicants,
            'types_educational_activities' => $this->types_educational_activities,
            'number_acquirers' => $this->number_acquirers,
            'limitation' => json_decode($this->limitation),
            'published' => $this->published
        ];
    }
}
