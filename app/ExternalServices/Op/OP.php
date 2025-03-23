<?php

namespace App\ExternalServices\Op;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OP
{
    private $expirationTime;

    private const HOST = 'https://op.sumdu.edu.ua/api/';

    public function __construct()
    {
        $this->expirationTime = now()->addHours(2);
    }

    protected function url($method): string
    {
        return self::HOST . $method;
    }

    private function setQueryParams(array $params): array
    {
        return array_merge($params ?? []);
    }
    /**
     * @param string $url
     * @param array|null $queryParams
     * @return Collection
     */
    protected function getOpData(string $url, ?array $queryParams, ?string $cacheName): Collection
    {
        if (!is_null($cacheName) && Cache::has($cacheName)) {
            return Cache::get($cacheName);
        }

        $results = Http::retry(3, 100)->get($url, $this->setQueryParams($queryParams))->json();


        if (!is_null($cacheName) && !Cache::has($cacheName)) {
            Cache::put($cacheName, collect($results), $this->expirationTime);
        }

        return collect($results);
    }

    public function getPublishedDocuments(): Collection
    {
        $url = $this->url('get-plans-ids');

        return  $this->getOpData($url, [], 'OP_published_documents');
    }
}
