<?php

namespace Tests\Unit\Http\Requests;

use App\Models\Signature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignatureRequestTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'signatures.';

    public function testisRequiredFields()
    {
        $this->actingAsUser();

        $validatedTypeFields = ['plan_id', 'position_id', 'asu_id'];
        $brokenRule = null;
        $brokenArray = array_fill_keys($validatedTypeFields, $brokenRule);

        $Setting = Signature::factory()->make($brokenArray);

        $this->postJson(route("{$this->route}store", $Setting->toArray()))
            ->assertJsonValidationErrors($validatedTypeFields);
    }
}
