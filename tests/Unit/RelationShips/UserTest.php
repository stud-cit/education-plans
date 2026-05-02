<?php

namespace Tests\Unit\RelationShips;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreateUser()
    {
        $user = User::factory()->create();

        $this->assertModelExists($user);
    }

    public function testCanChangeRole ()
    {
        $user = User::factory()->withRole(User::FACULTY_INSTITUTE)->create();

        $user->assignRole(User::ADMIN);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'role_id' => User::ADMIN
        ]);
    }
}
