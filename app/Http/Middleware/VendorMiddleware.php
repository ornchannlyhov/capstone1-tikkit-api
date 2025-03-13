<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VendorMiddleware
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You must be logged in to access this resource.'
            ], 401);
        }

        // Check if user is a vendor
        if (auth()->user()->role !== 'vendor') {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Access denied. Only vendors can perform this action.'
            ], 403);
        }

        return $next($request);
    }
}
