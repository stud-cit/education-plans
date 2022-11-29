<?php

namespace App\Http\Resources\CatalogSpeciality;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'user_id' => $this->id,
            'actions' => [
                'preview' => true,
                'edit' => true,
                'copy' => true,
                'delete' => true,
            ]
        ];
    }
}
