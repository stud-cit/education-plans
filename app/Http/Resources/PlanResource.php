<?php

namespace App\Http\Resources;

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
            'is_template' =>  $this->parent_id ? __('variables.Plan') : __('variables.Template'),
            'actions' => $this->actions()
        ];
    }
}
