<?php

namespace App\ExternalServices\Asu;

use App\Helpers\Helpers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ASU
{
    protected $asu_key;
    private $expirationTime;

    private const HOST = 'https://asu.sumdu.edu.ua/api/';
    protected const ID_INSTITUTE = 7;
    protected const ID_FACULTY = 9;
    protected const ID_DEPARTMENT = 2;
    // protected const REJECTED_UNITS = [1571, 1150, 382];
    // protected const REJECTED_DIVISIONS = [339, 380];
    protected const NOT_FOUND = 'Ідентифікатор не знайдено.';
    protected const ASU_ERRORS = ['ERROR_API', 'ERROR_CABINET'];

    public function __construct() {
        $this->asu_key = config('app.asu_key');
        $this->expirationTime = now()->addHours(24);
    }

    /**
     * @return string
     */
    public function getAsuKey(): string
    {
        return $this->asu_key;
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
        $status = $response['status'];
        $results = $response['result'];

        if (in_array($status, self::ASU_ERRORS)) {
            $message = "ASU: $results";
            Log::error($message);
            throw new HttpException ( 500, $message);
        }

        if (!empty($replaceKeys) && $status === 'OK') {
            $results = Helpers::replaceKeysInArray($results, $replaceKeys);
        }

        if (!is_null($cacheName) && !Cache::has($cacheName)) {
            Cache::put($cacheName, collect($results), $this->expirationTime);
        }

        return collect($results);
    }
}
