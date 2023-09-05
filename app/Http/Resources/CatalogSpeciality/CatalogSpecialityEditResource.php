<?php

namespace App\Http\Resources\CatalogSpeciality;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogSpecialityEditResource extends JsonResource
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
            'owners' => $this->owners
        ];
    }
}
