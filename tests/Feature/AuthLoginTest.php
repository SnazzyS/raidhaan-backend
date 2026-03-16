<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_mixed_case_email(): void
    {
        $user = User::factory()->create([
            'email' => 'suhail.1994@hotmail.com',
            'password' => Hash::make('ThatSnazzy7552'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'Suhail.1994@hotmail.com',
            'password' => 'ThatSnazzy7552',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
