<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "id" => $this->id,
            "asu_id" => $this->asu_id,
            "department_id" => $this->department_id,
            'faculty' => $this->facultyName,
            'department' => $this->departmentName,
            "faculty_id" => $this->faculty_id,
            "role_id" => $this->role_id,
        ];
    }
}
