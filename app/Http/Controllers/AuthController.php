<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('wallet_app')->plainTextToken;
        
            return response()->json([
                'message' => 'Autenticado',
                'token' => $token,
            ], 200);
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    public function logout() :JsonResponse {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
