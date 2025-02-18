<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    // Handle API user login.
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

    // Handle web admin login with sessions.
    public function adminLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role !== 'admin') {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'You do not have admin access.',
                ]);
            }

            ActivityLogHelper::logActivity($user, 'LogIn', 'Admin logged in');
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Handle API user logout (Token-based).
    public function apiLogout(Request $request): JsonResponse
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

    // Handle web admin logout (Session-based).
    public function adminLogout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogHelper::logActivity($user, 'LogOut', 'Admin logged out');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Successfully logged out.');
    }
}
