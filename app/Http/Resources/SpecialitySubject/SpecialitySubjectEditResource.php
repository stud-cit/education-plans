<?php

namespace App\Http\Resources\SpecialitySubject;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SubjectLanguage\SubjectLanguageListShowResource;

class SpecialitySubjectEditResource extends JsonResource
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
            'catalog_subject_id' => $this->catalog_subject_id,
            'id' => $this->id,
            'discipline' => [
                'id' => $this->asu_id,
                'title' => $this->title,
                'title_en' => $this->title_en,
            ],
            'language' => SubjectLanguageListShowResource::collection($this->languages),
            'lecturers' => $this->lecturers,
            'practice' => $this->practice,
            'faculty_id' => $this->faculty_id,
            'department_id' => $this->department_id,
            'list_fields_knowledge' => json_decode($this->list_fields_knowledge),
            'general_competence' => $this->general_competence,
            'learning_outcomes' => $this->learning_outcomes,
            'entry_requirements_applicants' => $this->entry_requirements_applicants,
            'types_educational_activities' => $this->types_educational_activities,
            'number_acquirers' => $this->number_acquirers,
            'limitation' => json_decode($this->limitation),
            'url' => $this->url,
            'published' => $this->published
        ];
    }
}
