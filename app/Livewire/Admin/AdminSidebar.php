<?php

namespace App\Livewire\Admin;

use App\Services\ModuleAccessService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdminSidebar extends Component
{
    public function render()
    {
        return view('livewire.admin.admin-sidebar');
    }

    public $topLevelModules;
    public $groupedModules;
    public $currentRoute;
    public $openSections = [];

    public function mount()
    {
        $this->loadModules();
        $this->currentRoute = request()->route()->getName();
        $this->initializeOpenSections();
    }

    public function loadModules()
    {
        if (Auth::check() && Auth::user()->user_role !== 'emp') {
            $modules = ModuleAccessService::getAccessibleModulesForSidebar();
            $this->topLevelModules = $modules['topLevel'];
            $this->groupedModules = $modules['grouped'];
        } else {
            $this->topLevelModules = collect();
            $this->groupedModules = collect();
        }
    }

    public function initializeOpenSections()
    {
        // Auto-open sections that contain the current active route
        foreach ($this->groupedModules as $parentId => $group) {
            $childModules = $group['modules'];
            $anyChildActive = $childModules->contains(function($module) {
                return $this->currentRoute === $module->route_name;
            });
            
            if ($anyChildActive) {
                $this->openSections[$parentId] = true;
            }
        }
    }

    public function toggleSection($parentId)
    {
        $this->openSections[$parentId] = !($this->openSections[$parentId] ?? false);
    }

    public function refreshModules()
    {
        // Clear any cached module data
        if (Auth::check()) {
            $user = Auth::user();
            ModuleAccessService::clearUserModuleCache($user->id, $user->user_role);
        }
        
        $this->loadModules();
        $this->initializeOpenSections();
    }

    public function isRouteActive($routeName)
    {
        return $this->currentRoute === $routeName;
    }

    public function isSectionActive($parentId)
    {
        if (!isset($this->groupedModules[$parentId])) {
            return false;
        }

        $childModules = $this->groupedModules[$parentId]['modules'];
        return $childModules->contains(function($module) {
            return $this->currentRoute === $module->route_name;
        });
    }

    public function isSectionOpen($parentId)
    {
        return $this->openSections[$parentId] ?? false;
    }
}
