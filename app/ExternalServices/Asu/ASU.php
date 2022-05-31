<?php

namespace App\ExternalServices\Asu;

use App\Helpers\Helpers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ASU
{
    private $asu_key;
    private $expirationTime;

    private const HOST = 'https://asu.sumdu.edu.ua/api/';
    protected const ID_INSTITUTE = 7;
    protected const ID_FACULTY = 9;
    protected const ID_DEPARTMENT = 2;
    protected const REJECTED_UNITS = [1571, 1150, 382];
    protected const REJECTED_DIVISIONS = [339, 380];
    protected const NOT_FOUND = 'Ідентифікатор не знайдено.';

    public function __construct() {
        $this->asu_key = config('app.asu_key');
        $this->expirationTime = now()->addHours(24);
    }

    protected function url($method) : string
    {
        return self::HOST . $method;
    }

    private function setQueryParams(Array $params): array
    {
        return array_merge(['key' => $this->asu_key], $params ?? []);
    }
    /**
     * @param string $url
     * @param array|null $queryParams
     * @param string|null $cacheName
     * @param array $replaceKeys
     * @return Collection
     */
    protected function getAsuData(string $url, ?array $queryParams, ?string $cacheName, array $replaceKeys = []): Collection
    {
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
}
