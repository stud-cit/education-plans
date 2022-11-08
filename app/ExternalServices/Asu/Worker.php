<?php

declare(strict_types=1);

namespace App\ExternalServices\Asu;

use Illuminate\Support\Collection;

class Worker extends ASU
{
    protected const WORKERS_TYPES = [
        'EMPLOYEE' => 2,
        'SUPERVISORS' => 4,
        'MANAGERS' => 8,
    ];

    public function __construct()
    {
        parent::__construct();
        $this->asu_key = config('app.asu_key_scipub');
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
        $worker = $this->getWorker($asu_id);

        if (empty($worker)) {
            return self::NOT_FOUND;
        }

        return $worker['last_name'] . ' ' . $worker['first_name'] . ' ' . $worker['patronymic'];
    }

    public function getShortName($asu_id)
    {
        $worker = $this->getWorker($asu_id);

        if (empty($worker)) {
            return self::NOT_FOUND;
        }

        return $worker['last_name'].' '.
            mb_substr($worker['first_name'], 0, 1).'.'.
            mb_substr($worker['last_name'], 0, 1). '.';
    }

    /**
     * @param $asu_id
     * @return Collection
     */

    public function getWorker($asu_id): array
    {
        $workers = $this->getAllWorkers();

        $worker = $workers->first(function ($item) use ($asu_id) {
            return $item['asu_id'] === $asu_id;
        });

        return $worker ?? [];
    }

    /**
     * @param $asu_id
     * @param $attribute
     * @return string
     */
    public function getWorkerAttribute($asu_id, $attribute = 'asu_id'): string
    {
        $worker = $this->getWorker($asu_id);

        if (empty($worker)) {
            return self::NOT_FOUND;
        }

        return $worker[$attribute];
    }
}
