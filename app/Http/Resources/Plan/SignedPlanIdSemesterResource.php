<?php

namespace App\Http\Resources\Plan;

use App\Http\Resources\Cycle\CycleApiResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Cycle\CycleApiSemesterResource;

class SignedPlanIdSemesterResource extends JsonResource
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
            'year' => $this->year,
            'education_program' => $this->educationProgramIdNameWithType[0],
            'education_program_type' => $this->educationProgramIdNameWithType[1],
            'faculty_id' => $this->faculty_id,
            'department_id' => $this->department_id,
            'unit' => "{$this->facultyName} {$this->departmentName}",
            'qualification' => $this->qualificationIdName,
            'field_knowledge' => $this->fieldKnowledgeIdName,
            'speciality' => $this->specialityIdName,
            'specialization' => $this->specializationIdName,
            'education_level' => $this->educationLevel->title,
            'cycles' => CycleApiSemesterResource::collection($this->cycles->whereNull('cycle_id')),
        ];
    }
}
