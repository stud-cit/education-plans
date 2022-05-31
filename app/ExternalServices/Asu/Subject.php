<?php

namespace App\ExternalServices\Asu;

use Illuminate\Support\Collection;

class Subject extends ASU
{
    public function getSubjects(): Collection
    {
        $url = $this->url('getDisciplines');
        $keys = [
            'ID_DISC' => 'id',
            'NAME_DISC' => 'title'
        ];
        return  $this->getAsuData($url, [], 'subjects', $keys);
    }
}
