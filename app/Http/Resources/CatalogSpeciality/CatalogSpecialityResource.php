<?php

namespace App\Http\Resources\CatalogSpeciality;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\VerificationCatalogResource;

class CatalogSpecialityResource extends JsonResource
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
            'speciality_id' => $this->speciality_id,
            'speciality' => $this->specialityIdName,
            'department_id' => $this->department_id,
            'department' => $this->departmentName,
            'faculty_id' => $this->faculty_id,
            'faculty' => $this->facultyName,
            'education_level' => $this->educationLevel->title,
            'education_level_id' => $this->educationLevel->id,
            'user_id' => $this->user_id,
            'user_verifications' => VerificationCatalogResource::collection($this->verifications),
            'need_verification' => $this->need_verification,
            'status' => $this->status,
            'actions' => $this->actions()
        ];
    }
}
