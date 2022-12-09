<?php

namespace App\Http\Resources\CatalogSpeciality;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogSpecialityPdfResource extends JsonResource
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
            'faculty' => $this->facultyName,
            'department' => $this->departmentName,
            'speciality' => $this->specialityIdName,
            'education_level' => $this->educationLevel->title,
            'subjects' => $this->subjects,
            'signatures' => $this->signatures,
        ];
    }
}
