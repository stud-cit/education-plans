<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Http\Constant;
use Illuminate\Support\Collection;

class Helpers
{
    /**
     * @param array $array
     * @param array $newKeys
     * @return array
     */
    static public function replaceKeysInArray(array $array, array $newKeys): array
    {
        $newArray = [];

        foreach ($array as $a) {
            $new_a = [];
            foreach ($newKeys as $key => $value) {
                if (is_null($value))
                    continue;

                if (array_key_exists($key, $a))
                    $new_a[$value] = $a[$key];
            }
            $newArray[] = $new_a;
        }

        return $newArray;
    }

    /**
     * @param array $source
     * @param array $fields
     * @return array
     */

    static public function uniqueByFields(array $source, array $fields): array
    {
        $filtered = array_reduce($source, function ($filtered, $item) use ($fields) {
            $key = array_reduce($fields, function ($key, $field) use ($item) {
                return $key . '_' . $item[$field];
            });
            $filtered[$key] = $item;

            return $filtered;
        });

        return array_values($filtered);
    }

    /**
     * @param Collection $collection
     * @param array $options
     * @return Collection
     */

    static public function searchCollection(Collection $collection, array $options): Collection
    {
        $result = [];

        if (!empty($options)) {
            $result = $collection->filter(function ($item) use ($options) {
                foreach ($options as $key => $value) {
                    //                    if ($item[$key] != $value) {
                    if (str_replace(["'", '`', '’'], '`', $item[$key]) != str_replace(["'", '`', '’'], '`', $value)) {
                        return false;
                    }
                }
                return true;
            });
        }

        return $result;
    }

    /**
     * @param $array
     * @param $columns
     */

    static public function removeColumnInArray(&$array, $columns)
    {
        array_walk($array, function (&$a) use ($columns) {
            foreach ($columns as $column)
                unset($a[$column]);
        });
    }

    static public function getPerPage($items_per_page, $arr)
    {
        return array_key_exists($items_per_page, $arr)
            ? $arr[$items_per_page]
            : Constant::PAGINATE;
    }

    static public function calculateEndYear($year, $studyTerm): int
    {
        $studyTermYear = $studyTerm->year;
        $studyTermMonth = $studyTerm->month ? 1 : 0;
        return $year + $studyTermYear + $studyTermMonth;
    }
}
