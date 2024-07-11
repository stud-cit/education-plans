<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerifyAsuApiKeyTest extends TestCase
{
    private $route = 'ep.';

    public function test_fail_without_api_key(): void
    {
        $response = $this->getJson($this->epRoute('index'));
        $response->assertStatus(403);
    }

    public function test_fail_with_wrong_api_key(): void
    {
        $response = $this->withHeaders([
            'X-API-Key' => 'wrong api key'
        ])->getJson($this->epRoute('index'));

        $response->assertStatus(403);
    }

    public function test_success_with_correct_api_key(): void
    {
        $response = $this->withHeaders([
            'X-API-Key' => config('app.protect_asu_api_key')
        ])->getJson($this->epRoute('index'));

        $response->assertStatus(200);
    }

    protected function epRoute($method)
    {
        return route("{$this->route}{$method}");
    }
}
