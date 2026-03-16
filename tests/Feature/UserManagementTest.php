<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_user_with_a_role(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Service Staff',
            'email' => 'staff@example.com',
            'role' => 'staff',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'staff@example.com',
            'role' => 'staff',
        ]);
    }

    public function test_staff_cannot_access_user_management(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $response = $this->actingAs($staff)->get('/users');

        $response->assertRedirect('/');
    }

    public function test_last_admin_cannot_be_demoted(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->from('/users')->actingAs($admin)->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'staff',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect('/users');
        $response->assertSessionHasErrors(['role']);

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->from('/users')->actingAs($admin)->delete("/users/{$admin->id}");

        $response->assertRedirect('/users');
        $response->assertSessionHas('error', 'You cannot delete your own account.');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }
}
