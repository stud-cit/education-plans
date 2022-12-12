<?php

namespace App\ExternalServices\Asu;

use App\Helpers\Helpers;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Profession extends ASU
{
    protected const FIELD_KNOWLEDGE_ID = 5; // ГАЛУЗЬ ЗНАЬ
    protected const SPECIALITY_ID = 2; // СПЕЦІАЛЬНІСТЬ
    protected const SPECIALIZATION_ID = 3; // СПЕЦІАЛІЗАЦІЯ
    protected const EDUCATION_PROGRAM_ID = 9; // 9 Освітня програма
    protected const EDUCATION_PROGRAM_ONP_ID = 11; // 11 Освітньо-наукова програма


    private const REMOVE_KEYS = ['parent_id', 'label_id', 'label'];

    // TODO: DUPLICATE CODE LIKE getName. How do better?
    public function getTitle($id, $key): string
    {
        $isExists = $this->getProfessions()->contains('id', $id);

        return $isExists ? $this->getProfessions()->firstWhere('id', $id)[$key] : self::NOT_FOUND;
    }

    public function getSpecializations(int $id): array
    {
        return $this->getFiltered(self::SPECIALIZATION_ID, $id);
    }

    public function getSpecialties(int $id): array
    {
        $filtered = $this->getFiltered(self::SPECIALITY_ID, $id);

        $collection = collect($filtered)->map(function ($item) {
            return [
                'id' => (int) $item['id'],
                'title' => "{$item['code']} {$item['title']}"
            ];
        });

        return $collection->values()->all();
    }

    public function getFieldKnowledge(): array
    {
        $fieldKnowledge = $this->getFiltered(self::FIELD_KNOWLEDGE_ID);
        $collectWithCode = collect($fieldKnowledge)->map(function ($item) {
            $ucFirstTitle = Str::ucfirst($item['title']);
            return [
                'id' => (int) $item['id'],
                'title' => "{$item['code']} $ucFirstTitle",
            ];
        });

        return $collectWithCode->values()->all();
    }

    public function getEducationPrograms(int $id): array
    {
        $filtered = collect($this->getFiltered(self::EDUCATION_PROGRAM_ID, $id));

        $currentLocale = setlocale(LC_ALL, NULL);
        setlocale(LC_ALL, 'uk_UA.utf8');

        $sorted = $filtered->sortBy('title', SORT_LOCALE_STRING)->values()->all();
        Helpers::removeColumnInArray($sorted, self::REMOVE_KEYS);
        setlocale(LC_ALL, $currentLocale);

        return $sorted;
    }

    public function getAllEducationPrograms()
    {
        $educationPrograms = $this->getFiltered(self::EDUCATION_PROGRAM_ID);
        return collect($educationPrograms)->map(function ($item) {
            $ucFirstTitle = Str::ucfirst($item['title']);
            return [
                'id' => (int) $item['id'],
                'title' => "{$item['code']} $ucFirstTitle",
            ];
        });
    }

    public function getAllSpecialties()
    {
        $specialties = $this->getFiltered(self::SPECIALITY_ID);

        return collect($specialties)->map(function ($item) {
            $ucFirstTitle = Str::ucfirst($item['title']);
            return [
                'id' => (int) $item['id'],
                'title' => "{$item['code']} $ucFirstTitle",
            ];
        });
    }

    // TODO: rename, bad name
    private function getFiltered(int $label_id, int $id = null): array
    {
        $filtered = $this->getProfessions()->filter(function ($value) use ($id, $label_id) {
            if ($id) {
                return $value['parent_id'] == $id && $value['label_id'] == $label_id;
            } else {
                return $value['label_id'] == $label_id;
            }
        });

        $filteredArray = $filtered->values()->all();
        Helpers::removeColumnInArray($filteredArray, self::REMOVE_KEYS);

        return $filteredArray;
    }

    private function getProfessions(): Collection
    {
        $url = $this->url('getProfessions');
        $keys = [
            'ID_PROF' => 'id',
            'ID_PAR' => 'parent_id',
            'NAME_PROF' => 'title',
            'KOD_INCL' => 'label_id', // 'Код вида' from documentation
            'NAME_INCL' => 'label',
            'CODE_PROF' => 'code'
        ];
        return  $this->getAsuData($url, [], 'professions', $keys);
    }
}
