<?php

namespace App\ExternalServices\Op;

use App\Helpers\Helpers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OP
{
    private $expirationTime;

    private const HOST = 'https://op.sumdu.edu.ua/api/';

    public function __construct() {
      $this->expirationTime = now()->addHours(24);
    }

    protected function url($method) : string
    {
        return self::HOST . $method;
    }

    private function setQueryParams(Array $params): array
    {
        return array_merge($params ?? []);
    }
    /**
     * @param string $url
     * @param array|null $queryParams
     * @return Collection
     */
    protected function getOpData(string $url, ?array $queryParams): Collection
    {
        $results = Http::retry(3, 100)->get($url, $this->setQueryParams($queryParams))->json();

        $collection = collect($results)->map(function ($item) {
          return [
              'program_id' => (int) $item['program_id'],
              'education_program_name' => "{$item['education_program_name']}, {$item['year']}, {$item['educational_degree']}"
          ];
        });

        return $collection;
    }

    public function getPrograms($request): Collection
    {
        $url = $this->url('get-programs-api');

        return  $this->getOpData($url, $request->all(), 'programs');
    }

    public function getProgramId($id): Collection
    {
        $url = $this->url('get-program-id-api');

        return  $this->getOpData($url, ["id" => $id]);
    }
}
