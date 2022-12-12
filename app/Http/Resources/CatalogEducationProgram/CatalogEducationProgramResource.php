<?php

namespace App\Http\Resources\CatalogEducationProgram;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\VerificationCatalogResource;

class CatalogEducationProgramResource extends JsonResource
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
            'year' => $this->year,
            'education_program_id' => $this->education_program_id,
            'education_program' => $this->educationProgramIdName,
            'department_id' => $this->department_id,
            'department' => $this->departmentName,
            'faculty_id' => $this->faculty_id,
            'faculty' => $this->facultyName,
            'education_level' => $this->educationLevel->title,
            'education_level_id' => $this->educationLevel->id,
            'user_id' => $this->user_id,
            'user_verifications' => VerificationCatalogResource::collection($this->verifications),
            'need_verification' => $this->need_verification,
            'actions' => $this->actions()
        ];
    }
}
