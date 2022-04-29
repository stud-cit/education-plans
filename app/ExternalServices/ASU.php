<?php declare(strict_types=1);

namespace App\ExternalServices;

use App\Helpers\Helpers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
    private const WORKERS_TYPES = [
        'EMPLOYEE' => 2,
        'SUPERVISORS' => 4,
        'MANAGERS' => 8,
    ];

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

    public function getFaculties(): Collection
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
    public function getFacultyName(int $id): string
    {
        $faculties = $this->getFaculties();

        return $this->getName($faculties, $id);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getShortFacultyName(int $id): string
    {
        $faculties = $this->getFaculties();

        return $this->getShortName($faculties, $id);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getDepartmentName(int $id): string
    {
        $departments = $this->getStructuralDepartment();

        return $this->getName($departments, $id);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getShortDepartmentName(int $id): string
    {
        $departments = $this->getStructuralDepartment();

        return $this->getShortName($departments, $id);
    }

    /**
     * @param $collection
     * @param $id
     * @return string
     */
    private function getName($collection, $id): string
    {
        $isExists = $collection->contains('id', $id);

        return $isExists ? $collection->firstWhere('id', $id)['name'] : self::NOT_FOUND;
    }

    /**
     * @param int $id
     * @return string
     */

    public function getDivisionName(int $id): string
    {
        $divisions = $this->getDepartments();

        return $this->getName($divisions, $id);
    }

    /**
     * @param $collection
     * @param $id
     * @return string
     */
    private function getShortName($collection, $id): string
    {
        $isExists = $collection->contains('id', $id);

        return $isExists ? $collection->firstWhere('id', $id)['short_name'] : self::NOT_FOUND;
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
            return $value['faculty_id'] == $structuralId && $value['unit_type'] == self::ID_DEPARTMENT;
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
            "ABBR_DIV" => "short_name",
        ];

        return  $this->getAsuData($this->url('getDivisions'), [], 'departments', $keys);
    }

    /**
     * @param string $type
     * @param bool $full
     * @return Collection
     */

    public function getWorkers(string $type = 'EMPLOYEE', Bool $full = true): Collection
    {
        $url = $this->url('getContingents');
        $queryParams = ['categ2' => self::WORKERS_TYPES[$type], 'mode' => $full];
        $keys = [
            "ID_FIO" => "asu_id",
            "F_FIO" => "last_name",
            "I_FIO" => "first_name",
            "O_FIO" => "patronymic",
            "KOD_DIV" => "department_id",
            "NAME_DIV" => "department",
        ];

        return $this->getAsuData($url, $queryParams, $type, $keys);
    }

    /**
     * @return Collection
     */

    public function getAllWorkers(): Collection
    {
        return $this->getWorkers('EMPLOYEE')
            ->merge($this->getWorkers('SUPERVISORS'))
            ->merge($this->getWorkers('MANAGERS'))->unique();
    }

    /**
     * @param $asu_id
     * @return String
     */

    public function getFullNameWorker($asu_id): String
    {
        $workers = $this->getAllWorkers();

        $worker = $workers->first(function ($item) use ($asu_id) {
            return $item['asu_id'] === $asu_id;
        });

        if (empty($worker)) {
            return self::NOT_FOUND;
        }

        return $worker['last_name'] .' '. $worker['first_name'] .' '. $worker['patronymic'];
    }

    public function searchFacultyByDepartmentId($department_id)
    {
        $divisions = $this->getDepartments();

        foreach ($divisions as $division)  {
            if ($division['id'] == $department_id) {
                if ($division['unit_type'] == self::ID_FACULTY || $division['unit_type'] == self::ID_INSTITUTE) {
                    return $division;
                } else {
                    return $this->searchFacultyByDepartmentId($division['faculty_id']);
                }
            }
        }
    }

    private function setQueryParams(Array $params): array
    {
        return array_merge(['key' => $this->asu_key], $params ?? []);
    }

}
