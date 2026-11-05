<?php

namespace App\Services;

use App\Exceptions\TokenRequestFailedException;
use Illuminate\Http\Request;

class PassportTokenService
{
    public function issueToken(string $email, string $password): array
    {
        $appRequest = app('request');

        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('passport.client_id'),
            'client_secret' => config('passport.client_secret'),
            'username' => $email,
            'password' => $password,
        ]);

        try {
            $response = app()->handle($tokenRequest);
        } finally {
            app()->instance('request', $appRequest);
        }

        $responseBody = json_decode($response->getContent(), true);

        if (! $response->isSuccessful()) {
            throw new TokenRequestFailedException(
                $response->getStatusCode(),
                $responseBody['error'] ?? null,
                $responseBody['error_description'] ?? null,
            );
        }

        return $responseBody;
    }
}
