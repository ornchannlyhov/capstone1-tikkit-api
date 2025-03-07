<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\InvalidStateException;
use GuzzleHttp\Exception\ClientException;
use Exception;

class SocialiteController extends Controller
{
    // Redirect user to the social provider's authentication page
    public function redirectToProvider($provider)
    {
        try {
            $supportedProviders = ['google', 'facebook'];

            if (!in_array($provider, $supportedProviders)) {
                return response()->json(['error' => 'Unsupported provider'], 400);
            }

            $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

            return response()->json(['redirect_url' => $redirectUrl]);
        } catch (Exception $e) {
            Log::error("Redirect Error ({$provider}): " . $e->getMessage());
            return response()->json(['error' => 'Failed to redirect to provider.'], 500);
        }
    }

    // Handle the callback from the social provider
    public function handleProviderCallback($provider)
    {
        try {
            $supportedProviders = ['google', 'facebook'];

            if (!in_array($provider, $supportedProviders)) {
                return response()->json(['error' => 'Unsupported provider'], 400);
            }

            $socialUser = Socialite::driver($provider)->stateless()->user();

            // Check if user exists
            $user = User::where('email', $socialUser->getEmail())
                ->where('provider', $provider)
                ->first();

            if (!$user) {
                // Create a new user if not found
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'provider_id' => $socialUser->getId(),
                    'provider' => $provider,
                    'password' => bcrypt(Str::random(24)),
                ]);

                ActivityLogHelper::logActivity($user, "User Registered via {$provider}");
            } else {
                ActivityLogHelper::logActivity($user, "User Logged In via {$provider}");
            }

            // Generate API token
            $token = $user->createToken('YourApp')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (InvalidStateException $e) {
            Log::warning("Invalid State Exception ({$provider}): " . $e->getMessage());
            return response()->json(['error' => 'Invalid authentication state. Please try again.'], 400);
        } catch (ClientException $e) {
            Log::warning("Client Exception ({$provider}): " . $e->getMessage());
            return response()->json(['error' => 'Failed to authenticate with provider.'], 401);
        } catch (Exception $e) {
            Log::error("Social Login Error ({$provider}): " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }
}
