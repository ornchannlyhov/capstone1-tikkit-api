<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function userLogin(LoginRequest $request): JsonResponse
    {
        try {
            $request->authenticate();
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            ActivityLogHelper::logActivity($user, 'LogIn', 'User logged in');

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function adminLogin(LoginRequest $request): JsonResponse
    {
        try {
            $request->authenticate();
            $user = Auth::user();

            if ($user->role !== 'admin') {
                return response()->json([
                    'message' => 'Unauthorized',
                    'error' => 'You do not have admin access'
                ], 403);
            }

            ActivityLogHelper::logActivity($user, 'LogIn', 'Admin logged in');
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Admin login successful',
                'user' => $user,
                'token' => $token
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found',
                ], 401);
            }

            ActivityLogHelper::logActivity($user, 'LogOut', 'User logged out');
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout successful'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
