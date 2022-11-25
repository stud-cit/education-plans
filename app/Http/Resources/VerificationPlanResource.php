<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VerificationPlanResource extends JsonResource
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
            'comment' => $this->comment,
            'role_id' => $this->role->role_id ?? null,
            'status' => $this->status,
            'plan_id' => $this->plan_id,
            'user_id' => $this->user_id,
            'verification_status_id' => $this->verification_statuses_id,
        ];
    }
}
