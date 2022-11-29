<?php

namespace App\Http\Resources\CatalogSpecialization;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogSpecializationResource extends JsonResource
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
            'year' => $this->year,
            'specialization_id' => $this->specialization_id,
            'specialization_name' => $this->specializationIdName,
            'user_id' => $this->id,
        ];
    }
}
