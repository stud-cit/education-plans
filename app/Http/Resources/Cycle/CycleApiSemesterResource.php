<?php

namespace App\Http\Resources\Cycle;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Subject\SubjectSemesterApiResource;

class CycleApiSemesterResource extends JsonResource
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
            "title" => $this->title,
            "subjects" => SubjectSemesterApiResource::collection($this->subjects),
            "cycles" => CycleApiSemesterResource::collection($this->cycles)
        ];
    }
}
