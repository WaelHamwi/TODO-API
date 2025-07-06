<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTGuard;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        /** @var \Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth(); 

        $token = $auth->attempt($credentials);

        if (! $token) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Return token + user
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $auth->factory()->getTTL() * 60,
            'user' => $auth->user(),
            'guard_class' => get_class($auth), 
        ]);
    }

    public function logout()
    {
        /** @var JWTGuard $auth */
        $auth = auth();
        $auth->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me()
    {
        /** @var JWTGuard $auth */
        $auth = auth();

        return response()->json($auth->user());
    }
}
