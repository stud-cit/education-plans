<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'settings.';

    public function testGetAllSettings(): void
    {
        Setting::factory()->count(3)->create();

        $response = $this->getJson(route("{$this->route}index"));

        $response->assertOk()->assertJsonStructure([
            'data' => [
                '*' => ['title', 'key', 'value']
            ]
        ]);
    }

    public function testCanStoreSetting(): void
    {
        $setting = Setting::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $setting->toArray());

        $response->assertCreated();

        $this->assertDatabaseHas('settings', $setting->toArray());
    }

    public function testCanShowSetting(): void
    {
        $existSetting = Setting::factory()->create();

        $response = $this->getJson(route("{$this->route}show", $existSetting->id));

        $response->assertOk()->assertExactJson([
            'data' => [
                'id' => $existSetting->id,
                'key' => $existSetting->key,
                'title' => $existSetting->title,
                'value' => $existSetting->value,
            ]
        ]);
    }

    public function testCanUpdateSetting(): void
    {
        $existSetting = Setting::factory()->create();
        $setting = Setting::factory()->make();

        $response = $this->putJson(
            route("{$this->route}update", $existSetting->id), $setting->toArray()
        );
        
        $response->assertStatus(200)->assertJson(['message' => __('messages.Updated')]);
    }

    public function testCanDeleteSetting(): void
    {
        $existSetting = Setting::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $existSetting->id));

        $response->assertOk()->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing('settings', $existSetting->toArray());
    }

}
