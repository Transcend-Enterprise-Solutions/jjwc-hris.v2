<?php

namespace App\Services;

use App\Models\AdminRoleAccess;
use App\Models\SystemModules;
use App\Models\ParentModules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ModuleAccessService
{
    /**
     * Check if current user has access to a specific route
     */
    public static function hasRouteAccess($routeName)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Cache key for user's accessible modules
        $cacheKey = "user_modules_{$user->id}_{$user->user_role}";
        
        // Get user's accessible modules (cached for performance)
        $accessibleModules = Cache::remember($cacheKey, 3600, function () use ($user) {
            $roleAccess = AdminRoleAccess::where('role_code', $user->user_role)->first();
            return $roleAccess ? $roleAccess->accessible_modules : [];
        });

        if (empty($accessibleModules)) {
            return false;
        }

        // Check if any accessible module matches this route
        $hasAccess = SystemModules::whereIn('id', $accessibleModules)
            ->where('route_name', $routeName)
            ->exists();

        return $hasAccess;
    }

    /**
     * Check if current user has access to a specific module ID
     */
    public static function hasModuleAccess($moduleId)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $roleAccess = AdminRoleAccess::where('role_code', $user->user_role)->first();
        
        if (!$roleAccess) {
            return false;
        }

        $accessibleModules = $roleAccess->accessible_modules;
        return in_array($moduleId, $accessibleModules);
    }

    /**
     * Get all accessible modules for current user
     */
    public static function getUserAccessibleModules()
    {
        if (!Auth::check()) {
            return collect();
        }

        $user = Auth::user();
        $roleAccess = AdminRoleAccess::where('role_code', $user->user_role)->first();
        
        if (!$roleAccess) {
            return collect();
        }

        return $roleAccess->module_objects; // Uses your accessor
    }

    /**
     * Get accessible modules grouped by parent for sidebar
     */
    public static function getAccessibleModulesForSidebar()
    {
        $accessibleModules = self::getUserAccessibleModules();
        
        if ($accessibleModules->isEmpty()) {
            return [
                'topLevel' => collect(),
                'grouped' => collect()
            ];
        }

        // Separate top-level and child modules
        $topLevelModules = $accessibleModules->whereNull('parent_module_id');
        $childModules = $accessibleModules->whereNotNull('parent_module_id');

        // Group child modules by parent
        $groupedModules = $childModules->groupBy('parent_module_id')
            ->map(function ($modules, $parentId) {
                $parent = ParentModules::find($parentId);
                return [
                    'parent' => $parent,
                    'modules' => $modules
                ];
            });

        return [
            'topLevel' => $topLevelModules,
            'grouped' => $groupedModules
        ];
    }

    /**
     * Clear user's module cache (call when role access is updated)
     */
    public static function clearUserModuleCache($userId, $roleCode)
    {
        $cacheKey = "user_modules_{$userId}_{$roleCode}";
        Cache::forget($cacheKey);
    }
}