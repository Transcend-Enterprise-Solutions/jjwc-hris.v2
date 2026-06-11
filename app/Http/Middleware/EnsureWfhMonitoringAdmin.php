<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWfhMonitoringAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->user_role, ['sa', 'hr', 'sv', 'pa'], true)) {
            return response()->json(['message' => 'WFH monitoring access denied.'], 403);
        }

        if (! $user->tokenCan('wfh-monitoring')) {
            return response()->json(['message' => 'This token cannot access WFH monitoring.'], 403);
        }

        return $next($request);
    }
}
