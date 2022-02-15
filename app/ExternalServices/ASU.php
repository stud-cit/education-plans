<?php declare(strict_types=1);

namespace App\ExternalServices;

use App\Http\Constant;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ASU
{
    private $asu_key;
    private $expirationTime;

    private const HOST = 'https://asu.sumdu.edu.ua/api/';
    private const ID_INSTITUTE = 7;
    private const ID_FACULTY = 9;
    private const ID_DEPARTMENT = 2;
    private const REJECTED_UNITS = [1571, 1150, 382];
    private const REJECTED_DIVISIONS = [339, 380];
    private const NOT_FOUND = 'Ідентифікатор не знайдено.';

    function __construct() {
        $this->asu_key = config('app.asu_key');
        $this->expirationTime = now()->addHours(24);
    }

    private function url($method) : string
    {
        return self::HOST . "{$method}";
    }

    /**
     * @param string $url
     * @param array|null $queryParams
     * @param string|null $cacheName
     * @param array $replaceKeys
     * @return Collection
     */
    private function getAsuData(string $url, ?array $queryParams, ?string $cacheName, array $replaceKeys = []): Collection
    {
    //  Cache::flush(); //if you need to clear all cache

        if (!is_null($cacheName) && Cache::has($cacheName)) {
            return Cache::get($cacheName);
        }

        $response = Http::retry(3, 100)->get($url, $this->setQueryParams($queryParams));

        if ($response['status'] === 'ERROR_API') {
            throw new HttpException ( 500,
                'ASU: '. $response['result']
            );
        }

        $results = $response['result'];

        if (!empty($replaceKeys)) {
            $results = Helpers::replaceKeysInArray($results, $replaceKeys);
        }

        if (!is_null($cacheName) && !Cache::has($cacheName)) {
            Cache::put($cacheName, collect($results), $this->expirationTime);
        }

        return collect($results);
    }

    public function getFaculty(): Collection
    {
        $filtered = $this->getDepartments()->filter(function ($value) {
            return $value['unit_type'] == self::ID_FACULTY;
        });

        return $filtered->values();
    }

    /**
     * @param int $id
     * @return string
     */
    public function getNameFacultyById(int $id): string
    {
        $faculties = $this->getFaculty();

        return $this->getName($faculties, $id);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getNameDepartmentById(int $id): string
    {
        $departments = $this->getStructuralDepartment();

        return $this->getName($departments, $id);
    }

    /**
     * @param $collection
     * @param $id
     * @return string
     */
    private function getName($collection, $id): string
    {
        $isExists = $collection->contains($id);

        return $isExists ? $collection->firstWhere('id', $id)['name'] : self::NOT_FOUND;
    }

    public function getStructuralDepartment(): Collection
    {
        $filtered = $this->getDepartments()->filter(function ($value) {
            return $value['unit_type'] == self::ID_DEPARTMENT;
        });

        return $filtered->values();
    }

    public function getDepartmentsByStructuralId($structuralId)
    {
        $filtered = $this->getDepartments()->filter(function ($value) use ($structuralId) {
            return $value['structural_id'] == $structuralId && $value['unit_type'] == self::ID_DEPARTMENT;
        });

        return $filtered->sortBy('department')->values();
    }

    public function getDepartments()
    {
        $keys = [
            "ID_DIV" => "id",
            "ID_PAR" => "faculty_id",
            "KOD_TYPE" => "unit_type",
            "KOD_DIV" => "department_id",
            "NAME_DIV" => "name",
        ];

        return  $this->getAsuData($this->url('getDivisions'), [], 'departments', $keys);
    }

    private function setQueryParams(Array $params): array
    {
        return array_merge(['key' => $this->asu_key], $params ?? []);
    }

}
