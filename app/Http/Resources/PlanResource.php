<?php

namespace App\Http\Resources;

use App\Http\Resources\VerificationPlanResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'title' => $this->title,
            'year' => $this->year,
            'study_term_id' => $this->study_term_id,
            'faculty_id' => $this->faculty_id,
            'faculty' => $this->facultyName,
            'short_faculty' => $this->shortFacultyName,
            'department_id' => $this->department_id,
            'department' => $this->departmentName,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'author_id' => $this->author_id,
            'author' => $this->author->name,
            'type_id' => $this->type->title,
            'actions' => $this->actions(),
            'published' => $this->published,
            'need_verification' => $this->need_verification,
            'user_verifications' => VerificationPlanResource::collection($this->user_verifications),
            'verification' => $this->approvedPlan ? __('variables.Verified') : __('variables.NotVerified'),
        ];
    }
}
