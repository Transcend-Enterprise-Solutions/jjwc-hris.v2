<?php

namespace App\Http\Middleware;

use App\Models\AdminRoleAccess;
use App\Models\SystemModules;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
   public function handle(Request $request, Closure $next, ...$role)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (in_array($user->user_role, $role)) {

                if($user->user_role === 'emp') {
                    return $next($request);
                }

                $routeName = $request->route()->getName();

                if ($this->shouldSkipModuleCheck($user->user_role, $routeName)) {
                    return $next($request);
                }

                if ($this->hasModuleAccess($user->user_role, $routeName)) {
                    return $next($request);
                }

                return redirect()->route('/dashboard')->with('error', 'Access denied to this module.');
            }

            if ($user->user_role === 'emp') {
                return redirect()->route('home');
            } else {
                return redirect()->route('/dashboard');
            }
        }

        return redirect('/login');
    }

    /**
     * Routes that should skip module access check
     */
    private function shouldSkipModuleCheck($roleCode, $routeName)
    {
        // Super admin has access to everything
        if ($roleCode === 'sa') {
            return true;
        }

        // Routes that should always be accessible
        $skipRoutes = [
            '/dashboard',
            'home',
            'login',
            'logout',
            'profile',
            'settings'
        ];

        return in_array($routeName, $skipRoutes);
    }

    /**
     * Check if user has access to specific module based on route
     */
    private function hasModuleAccess($roleCode, $routeName)
    {
        try {
            // Get admin role access for this role using role_code
            $roleAccess = AdminRoleAccess::where('role_code', $roleCode)
                ->first();
            
            if (!$roleAccess) {
                Log::warning("No role access found for role_code: {$roleCode}");
                return false;
            }

            // Get accessible modules for this role
            $accessibleModules = $roleAccess->modules;

            if (is_string($accessibleModules)) {
                $accessibleModules = explode(',', $accessibleModules);
            }
            
            if (empty($accessibleModules)) {
                Log::warning("No accessible modules for role_code: {$roleCode}");
                return false;
            }

            $systemModule = SystemModules::whereIn('id', $accessibleModules)
                ->where('route', $routeName)
                ->first();

            $hasAccess = $systemModule !== null;
            
            if (!$hasAccess) {
                Log::info("Module access check failed", [
                    'role_code' => $roleCode,
                    'route' => $routeName,
                    'modules' => $accessibleModules,
                    'found_modules' => SystemModules::whereIn('id', $accessibleModules)->pluck('route_name', 'id')->toArray()
                ]);
            }

            return $hasAccess;
            
        } catch (\Exception $e) {
            Log::error("Error checking module access: " . $e->getMessage(), [
                'role_code' => $roleCode,
                'route' => $routeName
            ]);
            return false;
        }
    }

}
