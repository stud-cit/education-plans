<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
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
            'credit' => $this->credit,
            'selective_discipline_id' => $this->selective_discipline_id,
            'cycle_id' => $this->cycle_id,
            'hours' => $this->hours,
            'practices' => $this->practices,
            'laboratories' => $this->laboratories
        ];
    }
}
