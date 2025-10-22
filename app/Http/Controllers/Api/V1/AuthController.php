<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Throwable;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $user = User::create([
                'id' => Str::uuid(),
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'account_type' => $data['account_type'],
                'organization_id' => $data['organization_id'] ?? null,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Registration successful',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database error during registration',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user and return token
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout the authenticated user
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to logout',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
