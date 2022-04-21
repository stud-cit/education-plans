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
        $validatedTypeField = 'key';
        $brokenRule = null;

        $existSetting = Setting::factory()->create();
        $newSetting = Setting::factory()->make([$validatedTypeField => $existSetting->key]);
//        dd($existStudyTerm->toArray());

        $this->patchJson(
            route("{$this->route}update", $existSetting),
            $newSetting->toArray()
        )->assertJsonMissingValidationErrors($validatedTypeField);
    }
}
