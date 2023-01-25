<?php

namespace App\ExternalServices\Asu;

use App\Helpers\Helpers;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Profession extends ASU
{
    protected const FIELD_KNOWLEDGE_ID = 5; // ГАЛУЗЬ ЗНАНЬ
    protected const SPECIALITY_ID = 2; // СПЕЦІАЛЬНІСТЬ
    protected const SPECIALIZATION_ID = 3; // СПЕЦІАЛІЗАЦІЯ
    protected const EDUCATION_PROGRAM_ID = 9; // 9 Освітня програма

    protected const EDUCATION_PROGRAM_OPP_ID = 10; // 10 Освітньо-професійна програма
    protected const EDUCATION_PROGRAM_ONP_ID = 11; // 11 Освітньо-наукова програма

    protected const EDUCATION_PROGRAM_TYPES = [
        self::EDUCATION_PROGRAM_ID => 'ОПП',
        self::EDUCATION_PROGRAM_OPP_ID => 'ОПП',
        self::EDUCATION_PROGRAM_ONP_ID => 'ОНП'
    ];


    private const REMOVE_KEYS = ['parent_id', 'label_id', 'label'];

    // TODO: DUPLICATE CODE LIKE getName. How do better?

    /**
     * @param $id
     * @param $key
     * @param $quote = true/false
     * @param $with = [$key => $position] $position (string) = after/before
     * @return string
     */
    public function getTitle($id, $key, $quote = false, $with = []): string
    {
        if (!$this->getProfessions()->contains('id', $id)) {
            return self::NOT_FOUND;
        }

        $profession = $this->getProfessions()->firstWhere('id', $id);

        $profession = $this->cloneOP($profession); //TODO - is problem name op in ASU

        $title = $quote ? '"'.$profession[$key].'"' : $profession[$key];

        if (count($with) > 0) {
            foreach ($with as $key => $position) {
                if ($position === 'after') {
                    $title = $profession[$key]. ' '. $title;
                } else if ($position === 'before') {
                    $title = $title. ' '. $profession[$key];
                }
            }
        }

        return $title;

        //$isExists = $this->getProfessions()->contains('id', $id);

        //return $isExists ? $this->getProfessions()->firstWhere('id', $id)[$key] : self::NOT_FOUND;
    }

    private function cloneOP($profession) {
        if ($profession['label_id'] == self::EDUCATION_PROGRAM_ID) {
            $profession['label'] = 'Освітньо-професійна програма';
        };

        return $profession;
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
        $educationPrograms = $this->sortProfessions();
        return collect($educationPrograms)->map(function ($item) {
            $ucFirstTitle = Str::ucfirst($item['title']);
            $type = self::EDUCATION_PROGRAM_TYPES[$item['label_id']];

            return [
                'id' => (int) $item['id'],
                'title' => "{$item['code']} $ucFirstTitle ($type)",
                'speciality_id' => $item['speciality_id'],
                'specialization_id' => $item['specialization_id']
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

    private function sortProfessions(): Collection
    {
        $professionsAll = $this->getProfessions()->keyBy('id');

        $professions = $professionsAll->filter(fn ($p) => in_array($p['label_id'], [
            self::EDUCATION_PROGRAM_ID, self::EDUCATION_PROGRAM_ONP_ID, self::EDUCATION_PROGRAM_OPP_ID
        ]));

        return $professions->map(function ($p) use ($professionsAll) {
            return $this->findParentKeysByEducationPrograms($p, $professionsAll);
        })->values();
    }

    /*
     * $p - first child;
     * $all - all collection getProfessions
     * $c - this child $profession
     */
    private function findParentKeysByEducationPrograms($p, $all, $c = null)
    {
        $parent = $all[$p['parent_id']];
        if (isset($parent)) {
            if ($parent['label_id'] == self::SPECIALITY_ID ) {
                if ($c !== null) {
                    $c['speciality_id'] = $parent['id'];
                    return $c;
                } else {
                    $p['speciality_id'] = $parent['id'];
                    $p['specialization_id'] = null;
                    return $p;
                }

            } else if ($parent['label_id'] == self::SPECIALIZATION_ID) {
                $p['specialization_id'] = $parent['id'];

                return $this->findParentKeysByEducationPrograms($parent, $all, $p);
            } else {
                return $c;
            }
        } else {
            return $p;
        }
    }

}
