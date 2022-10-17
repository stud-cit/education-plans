<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SignatureShowResource extends JsonResource
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
            'position' => $this->position->position,
            'agreed' => $this->position->agreed,
            'name' => $this->name,
            'surname' => mb_strtoupper($this->surname),
            'manual_position' => $this->manual_position
        ];
    }
}
