<?php

namespace App\Http\Resources;

use App\Http\Constant;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectsShowResource extends JsonResource
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
            "id" => $this->id,
            "cycle_id" => $this->cycle_id,
            "selective_discipline_id" => $this->selective_discipline_id,
            "asu_id" => $this->asu_id,
            "credits" => $this->credits,
            "hours" => $this->hours,
            "practices" => $this->practices,
            "laboratories" => $this->laboratories,
            "title" => $this->title,
            "selective_discipline" => $this->whenLoaded('selectiveDiscipline'),
            "semesters_credits" => $this->whenLoaded('semestersCredits')->pluck('credit','semester'),
            "hours_modules" => $this->whenLoaded('hoursModules'),
            "exams" => count($this->exams) ? $this->exams->first()->semester : '',
            "test" => count($this->test) ? $this->test->first()->semester : '',
            "individual_tasks" => count($this->individualTasks) ? $this->individualTasks->first()->semester : '',
            "total_volume_hour" => $this->credits * Constant::NUMBER_HOURS_IN_CREDIT,
        ];
    }
}
