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

    public function getEnglishTitle($id): string
    {
        $isExists = $this->getSubjects()->contains('id', $id);

        if (!$isExists) {
            return self::NOT_FOUND;
        }

        $en_title = $this->getSubjects()->firstWhere('id', $id)['title_en'];
        return $en_title ? $en_title : 'Відсутній відповідник англійською мовою';
    }

    public function getSubjects(): Collection
    {
        $url = $this->url('getDisciplines');
        $keys = [
            'ID_DISC' => 'id',
            'NAME_DISC' => 'title',
            'NAME_ENG' => 'title_en'
        ];
        return  $this->getAsuData($url, [], 'subjects', $keys);
    }
}
