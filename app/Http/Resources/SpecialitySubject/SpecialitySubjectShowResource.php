<?php

namespace App\Http\Resources\SpecialitySubject;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecialitySubjectShowResource extends JsonResource
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
            'subject_name' => $this->subjectName,
            'language' => $this->languages->map(function ($collection) {
                return $collection['language']['title'];
            })->implode(', '),
            'lecturers' => $this->getShortNames($this->lecturers),
            'practice' => $this->getShortNames($this->practice),
            'faculty_id' => $this->faculty_id,
            'faculty' => $this->facultyName,
            'department_id' => $this->department_id,
            'department' => $this->departmentName,
            'general_competence' => $this->general_competence,
            'learning_outcomes' => $this->learning_outcomes,
            'entry_requirements_applicants' => $this->entry_requirements_applicants,
            'types_educational_activities' => $this->types_educational_activities,
            'number_acquirers' => $this->number_acquirers,
            'limitation' => $this->limitationName,
            'user_id' => $this->user_id,
        ];
    }
}
