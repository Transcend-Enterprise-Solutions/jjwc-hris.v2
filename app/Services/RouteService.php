<?php

namespace App\Services;

use App\Models\AdminRoleAccess;
use App\Models\SystemModules;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class RouteService
{

    public static function registerDynamicRoutes()
    {
        if (! self::moduleTablesReady()) {
            return;
        }

        $modules = SystemModules::whereNotNull('route')
            ->whereNotNull('component_class')
            ->get();

        // Group modules by route to find which roles have access
        $routeRoleMap = [];

        foreach ($modules as $module) {
            $rolesWithAccess = AdminRoleAccess::whereRaw("FIND_IN_SET(?, modules)", [$module->id])
                ->pluck('role_code')
                ->toArray();

            if (!empty($rolesWithAccess)) {
                $routeRoleMap[$module->route] = [
                    'roles' => $rolesWithAccess,
                    'component' => $module->component_class,
                    'name' => $module->route
                ];
            }
        }

        // Register routes with appropriate middleware
        foreach ($routeRoleMap as $routePath => $routeData) {
            $roles = implode(',', $routeData['roles']);

            Route::middleware(['auth', "checkrole:{$roles}"])->group(function () use ($routePath, $routeData)   {
                Route::get($routePath, $routeData['component'])->name($routeData['name']);
            });
        }
    }

    /**
     * Get all routes that a specific role has access to
     */
    public static function getRoutesForRole($roleCode)
    {
        if (! self::moduleTablesReady()) {
            return collect();
        }

        $roleAccess = AdminRoleAccess::where('role_code', $roleCode)->first();

        if (!$roleAccess) {
            return collect();
        }

        $moduleIds = $roleAccess->accessible_modules;

        return SystemModules::whereIn('id', $moduleIds)
            ->whereNotNull('route')
            ->whereNotNull('component_class')
            ->get(['route', 'component_class', 'route_name', 'module_name']);
    }

    /**
     * Check if a role has access to a specific route
     */
    public static function roleHasRoute($roleCode, $routePath)
    {
        $routes = self::getRoutesForRole($roleCode);
        return $routes->contains('route', $routePath);
    }

    private static function moduleTablesReady(): bool
    {
        return Schema::hasTable('system_modules')
            && Schema::hasTable('admin_role_accesses');
    }
}
