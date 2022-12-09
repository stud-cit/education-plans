<?php

namespace App\Http\Resources\CatalogSpeciality;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CatalogSelectiveSubjectShowResource;

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
            'catalog' => [
                'faculty' => $this->facultyName,
                'department' => $this->departmentName,
                'speciality' => $this->specialityIdName,
                'education_level' => $this->educationLevel->title,
            ],
            'subjects' => CatalogSelectiveSubjectShowResource::collection($this->subjects),
            'signatures' => $this->signatures,
        ];
    }
}
