<?php

namespace App\Http\Resources\Asu;

use Illuminate\Http\Resources\Json\JsonResource;

class SchedulResource extends JsonResource
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
            'id' => $this['id'],
            'title' => $this['title'],
        ];
    }
}
