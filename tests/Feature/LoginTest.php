<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login()
    {
        // Arrange
        $client = Client::factory()->asPasswordClient()->create();
        config(['passport.client_id' => $client->id]);
        config(['passport.client_secret' => $client->plainSecret]);

        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
        $response->assertStatus(200);
    }

    public function test_login_fails_with_wrong_password()
    {
        // Arrange
        $client = Client::factory()->asPasswordClient()->create();
        config(['passport.client_id' => $client->id]);
        config(['passport.client_secret' => $client->plainSecret]);

        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password124',
            'password_confirmation' => 'password124',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_returns_500_when_client_is_misconfigured()
    {
        // Arrange
        $client = Client::factory()->asPasswordClient()->create();
        config(['passport.client_id' => $client->id]);
        config(['passport.client_secret' => $client->secret]); // wrong secret

        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password124',
            'password_confirmation' => 'password124',
        ]);

        $response->assertStatus(500);
    }
}
