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
            'department' => $this->shortDepartmentName,
            "selective_discipline" => $this->whenLoaded('selectiveDiscipline'),
            "semesters_credits" => $this->whenLoaded('semestersCredits')->pluck('credit','semester'),
            "hours_modules" => $this->whenLoaded('hoursModules'),
            "exams" => count($this->exams) ? $this->exams->first()->semester : '',
            "test" => count($this->test) ? $this->test->first()->semester : '',
//            "individual_tasks" => count($this->individualTasks) ? $this->individualTasks->first()->semester : '',
            "individual_tasks" => $this->getIndividualTasks($this->whenLoaded('hoursModules')),
            "total_volume_hour" => $this->credits * Constant::NUMBER_HOURS_IN_CREDIT,
        ];
    }

    private function getIndividualTasks($hours_modules)
    {
        $individual_tasks = '';
        $hours_modules->groupBy('individualTask.id')->map( function ($individual_task, $key) use (&$individual_tasks) {
            if (in_array($key, [
                Constant::INDIVIDUAL_TASKS['COURSE_WORK'],
                Constant::INDIVIDUAL_TASKS['CONTROL_WORK']
                ])
            ) {
                $individual_tasks .=
                    Constant::INDIVIDUAL_TASKS_SHORT[$key] . '('.
                    $individual_task->pluck('semester')->join(',') . ') ';
            }
        });
        return trim($individual_tasks);
    }
}
