<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProfileController extends Controller
{

    // Get the authenticated user's profile.
    public function edit(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'User profile retrieved successfully.',
                'data' => $request->user(),
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user profile.',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }


    //  Update the user's profile information.
    public function update(ProfileUpdateRequest $request)
    {
        try {
            $user = $request->user();
            $user->fill($request->validated());
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => $user,
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile.',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }


    // Delete the user's account.
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'current_password'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password.',
                    'errors' => $validator->errors(),
                    'status' => 422
                ], 422);
            }

            $user = $request->user();
            $user->tokens()->delete();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully.',
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting account.',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
