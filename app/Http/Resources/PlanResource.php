<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'faculty_id' => $this->faculty_id,
            'faculty' => $this->facultyName,
            'short_faculty' => $this->shortFacultyName,
            'department' => $this->departmentName,
            'created_at' => $this->created_at,
        ];
    }
}
