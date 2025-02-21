<?php
namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogHelper;
use Illuminate\Support\Facades\Log;

class SocialiteController extends Controller
{
    public function redirectToProvider($provider)
    {
        $supportedProviders = ['google', 'facebook'];
        if (!in_array($provider, $supportedProviders)) {
            return response()->json(['error' => 'Unsupported provider'], 400);
        }
        $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        return response()->json(['redirect_url' => $redirectUrl]);
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $user = User::where('email', $socialUser->getEmail())
                ->where('provider', $provider)
                ->first();

            if (!$user) {
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

            $token = $user->createToken('YourApp')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Social Login Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}
