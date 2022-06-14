<?php

namespace Tests\Unit\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingRequestTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'settings.';

    public function testKeyUpdateNotRequired()
    {
        $this->actingAsUser();

        $validatedTypeField = 'key';

        $existSetting = Setting::factory()->create();
        $newSetting = Setting::factory()->make([$validatedTypeField => $existSetting->key]);

        $this->patchJson(
            route("{$this->route}update", $existSetting),
            $newSetting->toArray()
        )->assertJsonMissingValidationErrors($validatedTypeField);
    }

    public function testKeyTitleValueIsRequiredWhenStore()
    {
        $this->actingAsUser();

        $validatedTypeFields = ['key', 'title', 'value'];
        $brokenRule = null;
        $brokenArray = array_fill_keys($validatedTypeFields, $brokenRule);

        $Setting = Setting::factory()->make($brokenArray);

        $this->postJson(route("{$this->route}store", $Setting->toArray()))
            ->assertJsonValidationErrors($validatedTypeFields);
    }

    public function testValueMax3chars()
    {
        $this->actingAsUser();

        $validatedTypeField = 'value';
        $brokenRule = random_int(1000, 9999);

        $Setting = Setting::factory()->make([$validatedTypeField => $brokenRule]);
        $existSetting = Setting::factory()->create();

        $this->postJson(route("{$this->route}store", $Setting->toArray()))
            ->assertJsonValidationErrors($validatedTypeField);

        // update
        $this->putJson(route("{$this->route}update", $existSetting->id), [$validatedTypeField => $brokenRule])
            ->assertJsonValidationErrors($validatedTypeField);
    }
}
