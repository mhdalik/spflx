<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|between:3,100',
            'email' => 'required|email|max:200|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create($validated);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 400);
        }

        $token = $user->createToken('default')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'user' => $user,
            'token' => $token
        ], 400);
    }
}
