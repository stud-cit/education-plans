<?php

namespace App\Http\Resources\CatalogSubject;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CatalogSelectiveSubjectShowResource;
use Illuminate\Support\Str;

class CatalogSubjectDisciplineResource extends JsonResource
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
            'year' => $this->year,
            'group_name' => Str::upper($this->group->title),
            'subjects' => CatalogSelectiveSubjectShowResource::collection($this->subjects),
        ];
    }
}
