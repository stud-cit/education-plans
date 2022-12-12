<?php

namespace App\Http\Resources\Asu;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkerResource extends JsonResource
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
            'asu_id' => $this['asu_id'],
            'full_name' => "{$this['last_name']} {$this['first_name']} {$this['patronymic']}",
        ];
    }
}
