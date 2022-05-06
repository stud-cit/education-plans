<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Collection;

class Tree
{
    private static function createTree($data): array
    {
        $parents = [];

        foreach ($data as $key => $item) {
            $parents[$item['cycle_id']][$item['id']] = $item;
        }

        $treeElem = $parents[null];
        self::generateElemTree($treeElem, $parents);
        
        return $treeElem;
    }

    private static function generateElemTree(&$treeElem, $parents): void
    {
        foreach ($treeElem as $key => $item) {
            if (array_key_exists($key, $parents)) {
                $treeElem[$key]['cycles'] = $parents[$key];
                self::generateElemTree($treeElem[$key]['cycles'], $parents);
            }
        }
    }

    private static function array_values_recursive($arr): array
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $arr[$key] = self::array_values_recursive($value);
            }
        }

        if (isset($arr['cycles'])) {
            $arr['cycles'] = array_values($arr['cycles']);
        }

        return $arr;
    }

    public static function makeTree(Collection $collection): array
    {
        $arr = $collection->toArray();

        $tree = self::createTree($arr);
        $data = array_values(self::array_values_recursive($tree));

        return $data;
    }
}
