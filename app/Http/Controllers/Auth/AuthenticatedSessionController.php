<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function userLogin(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = Auth::user();
        $token = $request->user()->createToken('API Token')->plainTextToken;

        ActivityLogHelper::logActivity($user, 'LogIn', 'User logged in');

        return response()->json([
            'message' => 'Login successful',
            'user' => Auth::user(),
            'token' => $token
        ]);
    }

    public function adminLogin(LoginRequest $loginRequest)
    {
        $loginRequest->authenticate();
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized']);
        }
        ActivityLogHelper::logActivity($user, 'LogIn', 'Admin logged in');
        return redirect()->route('admin.dashboard')->with('message', 'Admin login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'No authenticated user found'], 401);
        }

        ActivityLogHelper::logActivity($user, 'LogOut', 'User logged out');
        $user->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout successful']);
    }
}

