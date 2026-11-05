<?php

namespace App\Http\Controllers;

use App\Exceptions\TokenRequestFailedException;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Services\PassportTokenService;

class AuthController extends Controller
{
    public function __construct(
        private readonly PassportTokenService $tokenService,
    ) {}

    public function login(AuthLoginRequest $request): JsonResponse
    {
        try {
            $responseBody = $this->tokenService->issueToken($request->validated('email'), $request->validated('password'));

            return response()->json([
                'access_token' => $responseBody['access_token'],
                'token_type' => $responseBody['token_type'],
                'expires_in' => $responseBody['expires_in'],
            ]);
        } catch (TokenRequestFailedException $e) {
            if ($e->isInvalidCredentials()) {
                return response()->json([
                    'message' => 'These credentials do not match our records.',
                ], 401);
            }

            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function register(AuthRegisterRequest $request): JsonResponse
    {
        try {
            $result = DB::transaction(function () use ($request) {
                User::create([
                    'name' => $request->validated('name'),
                    'email' => $request->validated('email'),
                    'password' => $request->validated('password'), // password is hashed in the model
                ]);

                $responseBody = $this->tokenService->issueToken($request->validated('email'), $request->validated('password'));

                return [
                    'access_token' => $responseBody['access_token'],
                    'token_type' => $responseBody['token_type'],
                    'expires_in' => $responseBody['expires_in'],
                ];
            });

            return response()->json($result, 201);
        } catch (TokenRequestFailedException $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to create token',
            ], 500);
        }
    }
}
