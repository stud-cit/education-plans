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
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreateUser()
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();

        $this->assertModelExists($user);
    }

    public function testCanChangeRole ()
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create(['role_id' => 1]);

        $role = Role::find(2);
        $user->assignRole($role);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'role_id' => 2
        ]);

    }
}
