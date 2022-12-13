<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionsResource extends JsonResource
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
            'id' => (int) $this['id'],
            'title' => $this['title'],
            'speciality_id' => $this['speciality_id'] ?? null,
            'specialization_id' => $this['specialization_id'] ?? null,
        ];
    }
}
