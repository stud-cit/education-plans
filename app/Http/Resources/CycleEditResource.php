<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CycleEditResource extends JsonResource
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
            "cycle_id" => $this->cycle_id,
            "plan_id" => $this->plan_id,
            "title" => $this->title,
            "credit" => $this->credit,
            "list_cycle" => $this->listCycle,
            "list_cycle_id" => $this->listCycle->id,
            "has_discipline" => $this->has_discipline,
            "subjects" => SubjectsEditResource::collection($this->subjects),
            "cycles" => CycleEditResource::collection($this->cycles)
        ];
    }
}
