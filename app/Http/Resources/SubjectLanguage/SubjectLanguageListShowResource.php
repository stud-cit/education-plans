<?php

namespace App\Http\Resources\SubjectLanguage;

use Illuminate\Http\Resources\Json\JsonResource;

class SubjectLanguageListShowResource extends JsonResource
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
            'language_id' => $this->language_id,
            'subject_id' => $this->subject_id,
        ];
    }
}
