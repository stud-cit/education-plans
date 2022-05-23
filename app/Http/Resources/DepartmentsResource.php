<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentsResource extends JsonResource
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
            'faculty_id' => (int) $this['faculty_id'],
            'unit_type' => (int) $this['unit_type'],
            'name' => $this['name'],
            'short_name' => $this['short_name']
        ];
    }
}
