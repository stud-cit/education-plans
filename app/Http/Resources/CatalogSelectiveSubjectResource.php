<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogSelectiveSubjectResource extends JsonResource
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
            'year' => $this->selectiveCatalog->year,
            'title' => $this->title,
            'department_id' => $this->department_id,
            'department' => $this->departmentName,
            'group' => $this->selectiveCatalog->group->title,
            'status' => $this->status,
            'user_verifications' => VerificationSubjectResource::collection($this->verifications),
            'need_verification' => $this->need_verification,
            'published' => $this->published,
            'actions' => $this->actions(),
        ];
    }
}
