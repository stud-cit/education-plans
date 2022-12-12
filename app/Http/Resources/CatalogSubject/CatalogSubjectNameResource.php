<?php

namespace App\Http\Resources\CatalogSubject;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogSubjectNameResource extends JsonResource
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
            'title' => "{$this->year} рік. {$this->group->title}",
        ];
    }
}
