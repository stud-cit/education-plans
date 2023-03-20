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
            "full_name" => $this->name,
            "department_id" => $this->department_id,
            'department' => $this->department_name,
            "faculty_id" => $this->faculty_id,
            'faculty' => $this->faculty_name,
            "role_id" => $this->role_id
        ];
    }
}
