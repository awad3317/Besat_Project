<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Driver;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DriverMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\Driver $driver */
        $driver = auth('sanctum')->user();
        if ($driver && $driver instanceof Driver) {
            if ($driver->is_banned) {
                $driver->currentAccessToken()->delete(); 
                return response()->json(['message' => 'تم حظر حسابك من قبل الإدارة.'], 403);
            }
            if (!$driver->is_active) {
                return response()->json(['message' => 'حسابك غير نشط أو قيد المراجعة.'], 401);
            }
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized. Driver access required.'], 403);
    }
}
