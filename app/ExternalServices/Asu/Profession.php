<?php

namespace App\ExternalServices\Asu;

use App\Helpers\Helpers;
use Illuminate\Support\Collection;

class Profession extends ASU
{
    protected const SPECIALITY_ID = 2; // СПЕЦІАЛЬНІСТЬ
    protected const SPECIALIZATION_ID = 3; // СПЕЦІАЛІЗАЦІЯ
    protected const FIELD_KNOWLEDGE_ID = 5; // ГАЛУЗЬ ЗНАЬ
    protected const EDUCATION_PROGRAM_ID = 9; // 9 Освітня програма
    private const REMOVE_KEYS = ['parent_id', 'label_id', 'label'];

    // TODO: DUPLICATE CODE LIKE getName
    public function getTitle($id): string
    {
        $isExists = $this->getProfessions()->contains('id', $id);

        return $isExists ? $this->getProfessions()->firstWhere('id', $id)['title'] : self::NOT_FOUND;
    }

    public function getSpecialty(): array
    {
        return $this->getFiltered(self::SPECIALITY_ID);
    }

    public function getSpecializations(int $id): array
    {
        return $this->getFiltered(self::SPECIALIZATION_ID, $id);
    }

    public function getFieldKnowledge(): array
    {
        return $this->getFiltered(self::FIELD_KNOWLEDGE_ID);
    }

    public function getEducationPrograms(): array
    {
        $filtered = collect($this->getFiltered(self::EDUCATION_PROGRAM_ID));

        $currentLocale = setlocale(LC_ALL, NULL);
        setlocale(LC_ALL,'uk_UA.utf8');

        $sorted = $filtered->sortBy('title', SORT_LOCALE_STRING)->values()->all();
        Helpers::removeColumnInArray($sorted, self::REMOVE_KEYS);
        setlocale(LC_ALL, $currentLocale);

        return $sorted;
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
            'NAME_INCL' => 'label'
        ];
        return  $this->getAsuData($url, [], 'professions', $keys);
    }
}
