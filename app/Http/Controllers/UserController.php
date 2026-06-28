<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        summary: "Register a new user",
        description: "Creates a new user account with the provided details.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password"],
                properties: [
                    new OA\Property(property: "name", type: "string", minLength: 3, maxLength: 100, example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", maxLength: 200, example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", minLength: 6, example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User registered successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User registered successfully"),
                        new OA\Property(property: "user", ref: "#/components/schemas/User")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
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

    #[OA\Post(
        path: "/api/login",
        summary: "Login a user",
        description: "Logs in an existing user and returns a Sanctum authentication token.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", maxLength: 255, example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", minLength: 6, example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Login success"),
                        new OA\Property(property: "user", ref: "#/components/schemas/User"),
                        new OA\Property(property: "token", type: "string", example: "1|abcdef123456...")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid credentials",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Invalid credentials")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
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
        ], 200);
    }
}
