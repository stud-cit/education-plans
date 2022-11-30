<?php

namespace App\Http\Resources\SpecialitySubject;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecialitySubjectResource extends JsonResource
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
            'department_id' => $this->department_id,
            'department' => $this->departmentName,
            'published' => $this->published,
            'user_id' => $this->id,
        ];
    }
}
