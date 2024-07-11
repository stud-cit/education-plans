<?php

namespace App\Http\Resources\Api;

use App\ExternalServices\Asu\Worker;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectPdfResource extends JsonResource
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
            'list_fields_knowledge' => $this->list_fields_knowledge ? $this->listFieldsKnowledgeName : null,
            'education_level' => $this->catalog_education_level_id ? $this->educationLevel->title : null,
            'general_competence' => $this->general_competence,
            'learning_outcomes' => $this->learning_outcomes,
            'entry_requirements_applicants' => $this->entry_requirements_applicants,
            'types_educational_activities' => $this->types_educational_activities,
            'number_acquirers' => $this->number_acquirers,
            'limitation' => $this->limitationName
        ];
    }

    protected function getShortNames($listNames)
    {
        return $this->getShortName($listNames)->implode(', ');
    }

    protected function getShortName($collection)
    {
        $worker = new Worker();

        return $collection->map(function ($collection) use ($worker) {
            return $worker->getShortName($collection['asu_id']);
        });
    }
}
