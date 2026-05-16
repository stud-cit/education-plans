<?php

namespace Tests\Feature\Api;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'settings.';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
        $this->seed([RoleSeeder::class]);
    }

    public function testGetAllSettings(): void
    {
        $user = User::factory()->admin()->create();
        Setting::factory()->count(3)->create();

        $this->actingAs($user);
        $response = $this->getJson(route($this->route . "index"));

        $response->assertOk()->assertJsonStructure([
            'data' => [
                '*' => ['title', 'key', 'value', 'edit']
            ]
        ]);
    }

    public function testCanStoreSetting(): void
    {

        $user = User::factory()->withRole(User::ROOT)->create();
        $setting = Setting::factory()->make();

        $this->actingAs($user);
        $response = $this->postJson(route("{$this->route}store"), $setting->toArray());

        $response->assertCreated();

        $this->assertDatabaseHas('settings', $setting->toArray());
    }

    public function testCanShowSetting(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $existSetting = Setting::factory()->create();

        $response = $this->getJson(route("{$this->route}show", $existSetting->id));

        $response->assertOk()->assertExactJson([
            'data' => [
                'id' => $existSetting->id,
                'key' => $existSetting->key,
                'title' => $existSetting->title,
                'value' => $existSetting->value,
                'edit' => false,
            ]
        ]);
    }

    public function testCanUpdateSetting(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $existSetting = Setting::factory()->create();
        $setting = Setting::factory()->make();

        $response = $this->putJson(route("{$this->route}update", $existSetting->id), $setting->toArray());

        $response->assertStatus(200)->assertJson(['message' => __('messages.Updated')]);
    }

    public function testCanDeleteSetting(): void
    {
        $user = User::factory()->withRole(User::ROOT)->create();
        $this->actingAs($user);

        $existSetting = Setting::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $existSetting->id));

        $response->assertOk()->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing('settings', $existSetting->toArray());
    }

    public function testGuestCannotPossibilityUpdateTettings()
    {
        $guest = User::factory()->guest()->create();
        $this->actingAs($guest);

        $validatedTypeField = 'key';

        $existSetting = Setting::factory()->create();
        $newSetting = Setting::factory()->make([$validatedTypeField => $existSetting->key]);

        $this->patchJson(route("{$this->route}update", $existSetting), $newSetting->toArray())
            ->assertForbidden();
    }

    public function testRootKeyUpdateNotRequired()
    {
        $root = User::factory()->withRole(User::ROOT)->create();
        $this->actingAs($root);

        $validatedTypeField = 'key';

        $existSetting = Setting::factory()->create();
        $newSetting = Setting::factory()->make([$validatedTypeField => $existSetting->key]);

        $this->patchJson(
            route("{$this->route}update", $existSetting),
            $newSetting->toArray()
        )->assertStatus(200);
    }

    public function testKeyTitleValueIsRequiredWhenStore()
    {
        $root = User::factory()->withRole(User::ROOT)->create();

        $validatedTypeFields = ['key', 'title', 'value'];
        $brokenRule = null;
        $brokenArray = array_fill_keys($validatedTypeFields, $brokenRule);

        $Setting = Setting::factory()->make($brokenArray);

        $this->actingAs($root);
        $this->postJson(route("{$this->route}store", $Setting->toArray()))
            ->assertStatus(422)
            ->assertJsonValidationErrors($validatedTypeFields);
    }
}
