<?php

namespace App\Http\Resources\Subject;

use App\Models\FormControl;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectApiResource extends JsonResource
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
            "credits" => $this->credits,
            "title" => $this->selective_discipline_id ? $this->selectiveDiscipline->title : $this->title,
            "form_control" => $this->lastFormControl,
            "subjects" => SubjectApiResource::collection($this->subjects),
        ];
    }
}
