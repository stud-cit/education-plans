<?php

namespace App\Http\Resources\Cycle;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Subject\SubjectApiResource;

class CycleApiResource extends JsonResource
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
            "subjects" => SubjectApiResource::collection($this->subjects),
            "cycles" => CycleApiResource::collection($this->cycles)
        ];
    }
}
