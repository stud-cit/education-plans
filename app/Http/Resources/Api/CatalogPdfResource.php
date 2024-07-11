<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CatalogSignaturesResource;

class CatalogPdfResource extends JsonResource
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
                'year' => (int) $this->year,
            ],
            'subjects' => SubjectPdfResource::collection($this->subjects),
            'signatures' => CatalogSignaturesResource::collection($this->signatures),
        ];
    }
}
