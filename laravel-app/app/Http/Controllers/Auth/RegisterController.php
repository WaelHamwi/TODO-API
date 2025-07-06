<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{

    public function register(RegisterRequest $request)
    {

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);


        $roleName = $validated['role'] ?? 'Guest';

        $role = Role::firstOrCreate(
            ['name' => $roleName],
            ['slug' => strtolower($roleName)]
        );

        $user->roles()->attach($role);

        $token = JWTAuth::fromUser($user);

        /** @var \Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $auth->factory()->getTTL() * 60,
            'user' => $user->load('roles'),
        ]);
    }
}
