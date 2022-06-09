<?php

namespace App\ExternalServices\Asu;

use Illuminate\Support\Collection;

class Qualification extends ASU
{
    public function getQualifications(): array
    {
        return $this->getData()->values()->all();
    }

    // TODO: DUPLICATE CODE LIKE getName
    public function getTitle($id): string
    {
        $isExists = $this->getData()->contains('id', $id);

        return $isExists ? $this->getData()->firstWhere('id', $id)['title'] : self::NOT_FOUND;
    }

    private function getData(): Collection
    {
        $url = $this->url('getQualifications');
        $keys = [
            'ID_QUAL' => 'id',
            'NAME_QUAL' => 'title'
        ];
        return  $this->getAsuData($url, [], 'qualifications', $keys);
    }
}
