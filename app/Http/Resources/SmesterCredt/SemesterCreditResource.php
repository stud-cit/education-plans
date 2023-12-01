<?php

namespace App\Http\Resources\SmesterCredt;

use Illuminate\Http\Resources\Json\JsonResource;

class SemesterCreditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return  [
            "credit" => $this->credit,
            "course" => $this->course,
            "semester" => $this->semester
        ];
    }
}
