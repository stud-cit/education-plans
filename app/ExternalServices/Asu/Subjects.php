<?php

namespace App\ExternalServices\Asu;

use Illuminate\Support\Collection;

class Subjects extends ASU
{
    public function getTitle($id): string
    {
        $isExists = $this->getSubjects()->contains('id', $id);

        return $isExists ? $this->getSubjects()->firstWhere('id', $id)['title'] : self::NOT_FOUND;
    }
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
