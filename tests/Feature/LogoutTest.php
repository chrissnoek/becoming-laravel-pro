<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Client;


class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout(): void
    {
        // Arrange
        $client = Client::factory()->asPasswordClient()->create();
        config(['passport.client_id' => $client->id]);
        config(['passport.client_secret' => $client->plainSecret]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $token = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ])->json('access_token');

        $this->withToken($token)->postJson('/api/logout')->assertNoContent();
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/user')->assertUnauthorized();
        $this->assertDatabaseHas('oauth_refresh_tokens', ['revoked' => true]);
    }
}
