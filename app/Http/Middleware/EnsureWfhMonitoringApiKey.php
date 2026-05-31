<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWfhMonitoringApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = config('wfh_monitoring.api_key');

        if (! $expectedKey) {
            return response()->json([
                'message' => 'WFH monitoring API key is not configured.',
            ], 503);
        }

        $providedKey = $request->bearerToken() ?: $request->header('X-WFH-Monitoring-Key');

        if (! is_string($providedKey) || ! hash_equals($expectedKey, $providedKey)) {
            return response()->json([
                'message' => 'Invalid WFH monitoring API key.',
            ], 401);
        }

        return $next($request);
    }
}
