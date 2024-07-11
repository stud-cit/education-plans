<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class EducationPlanResource extends JsonResource
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
            'guid' => $this->guid,
            'year' => $this->year,
            'education_program_id' => $this->education_program_id,
            'education_program' => $this->educationProgramIdNameWithType ? $this->educationProgramIdNameWithType[0] : null,
            'education_program_type' => $this->educationProgramIdNameWithType ?  $this->educationProgramIdNameWithType[1] : null,
            'faculty_id' => $this->faculty_id,
            'faculty' => $this->facultyName,
            'department_id' => $this->department_id,
            'department' => $this->departmentName,
            'qualification' => $this->qualificationIdName,
            'qualification_id' => $this->qualification_id,
            'field_knowledge_id' => $this->field_knowledge_id,
            'field_knowledge' => $this->fieldKnowledgeIdName,
            'speciality' => $this->specialityIdName,
            'speciality_id' => $this->speciality_id,
            'education_level_id' => $this->education_level_id,
            'education_level' => $this->educationLevel->title,
        ];
    }
}
