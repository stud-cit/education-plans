<?php

namespace App\ExternalServices\Asu;

use Illuminate\Support\Collection;

class ProfessionQualification extends ASU
{
    public function getQualifications(): array
    {
        return $this->getData()->reject(function ($value) {
            return empty($value['title']);
        })->transform(function ($value) {
            $value['title'] = mb_ucfirst($value['title']);
            return $value;
        })->sortBy('title')->values()->all();
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
            'NAME_PQUAL' => 'title'
        ];
        return  $this->getAsuData($url, [], 'profesion_qualifications', $keys);
    }
}
