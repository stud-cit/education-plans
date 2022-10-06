<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogGroupResource extends JsonResource
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
            'deleted_at' => $this->deleted_at,
        ];
    }
}
