<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        // Arrange
        $client = Client::factory()->asPasswordClient()->create();
        config(['passport.client_id' => $client->id]);
        config(['passport.client_secret' => $client->plainSecret]);


        // Act
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $response->assertStatus(201);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_register_rolls_back_when_token_request_fails()
    {
        // Arrange
        $client = Client::factory()->asPasswordClient()->create();
        config(['passport.client_id' => $client->id]);
        config(['passport.client_secret' => $client->secret]); // wrong secret

        // Act
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
        $response->assertStatus(500);
    }
}
