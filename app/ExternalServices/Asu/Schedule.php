<?php

namespace App\ExternalServices\Asu;

use App\ExternalServices\Asu\ASU;
use Illuminate\Support\Collection;

class Schedule extends ASU
{
    protected function getData(): Collection
    {
        $url = $this->url('getGraphs');
        $keys = [
            'ID_GRAPH' => 'id',
            'NAME_GRAPH' => 'title',
            'ID_LEVEL' => 'education_level_id',
            'NAME_LEVEL' => 'eduaction_title',
            'ID_TERM' => 'study_term_id',
            'NAME_TERM' => 'study_term_title',
        ];

        return  $this->getAsuData($url, [], 'schedule_asu', $keys);
    }

    public function list(int $education_level_id, int $study_term_id): Collection
    {
        return $this->getData()->filter(function ($value) use ($education_level_id, $study_term_id) {
            return $value['education_level_id'] === $education_level_id && $value['study_term_id'] === $study_term_id;
        });
    }

    public function getItem(int $id)
    {
        $url = $this->url('getGraphs');
        $keys = [
            'COURSE' => 'course',
            'NUM_SEM' => 'num_sem',
            'NUM_MOD' => 'num_mod',
            'NUM_WEEK' => 'num_week',
            'KOD_WEEK' => 'kod_week',
            'NAME_WEEK' => 'name_week',
            'ABBR_WEEK' => 'val',
        ];

        return  $this->getAsuData($url, ['id' => $id], "schedule_asu_$id", $keys);
    }
}
