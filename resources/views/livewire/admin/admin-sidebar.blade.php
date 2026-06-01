<div>
    {{-- Top-level modules (standalone modules without parent) --}}
    @foreach($topLevelModules as $module)
        @if($module->route && $module->route !== 'dashboard')
            @php
                $opensWfhWall = $module->route === '/employee-management/wfh-monitoring';
                $moduleHref = $opensWfhWall ? route('wfh-monitoring.wall') : route($module->route);
            @endphp
            <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))]
            @if ($this->isRouteActive($module->route)) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                x-data="{ open: {{ $this->isRouteActive($module->route) ? 'true' : 'false' }} }">
                <a class="block text-gray-800 dark:text-gray-100 truncate transition
                @if ($this->isRouteActive($module->route)) {{ '!text-blue-500' }} @endif"
                    href="{{ $moduleHref }}"
                    @if($opensWfhWall)
                        target="_blank"
                        rel="noopener"
                    @endif>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            @if($module->icon)
                                <i class="{{ $module->icon }} text-slate-400 mr-3"></i>
                            @else
                                <i class="bi bi-circle text-slate-400 mr-3"></i>
                            @endif
                            <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                {{ $module->module_name }}
                            </span>
                        </div>
                    </div>
                </a>
            </li>
        @endif
    @endforeach

    {{-- Grouped modules (modules with parents) --}}
    @foreach($groupedModules as $parentId => $group)
        @php
            $parent = $group['parent'];
            $childModules = $group['modules'];
            $sectionActive = $this->isSectionActive($parentId);
            $sectionOpen = $this->isSectionOpen($parentId);
            $parentRoute = $parent->route ?? '#0';

            // Check if any child route is currently active
            $hasActiveChild = false;
            foreach($childModules as $childModule) {
                if($childModule->route && $this->isRouteActive($childModule->route)) {
                    $hasActiveChild = true;
                    break;
                }
            }

            // Parent is active if section is active OR has an active child
            $parentIsActive = $sectionActive || $hasActiveChild;
        @endphp

        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))]
            @if ($parentIsActive) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
            x-data="{
                open: @js($sectionOpen || $hasActiveChild),
                parentId: {{ $parentId }},
                init() {
                    // Listen for external section toggles to sync state
                    this.$watch('open', (value) => {
                        if (value !== @js($sectionOpen || $hasActiveChild)) {
                            this.$wire.toggleSection(this.parentId);
                        }
                    });
                }
            }">

            {{-- Parent module header --}}
            <a class="block text-gray-800 dark:text-gray-100 truncate transition"
                href="{{ $parentRoute !== '#0' ? route($parentRoute) : '#0' }}"
                @if($parentRoute === '#0')
                    @click.prevent="
                        if (sidebarExpanded) {
                            open = !open;
                        } else {
                            sidebarExpanded = true;
                        }
                    "
                @else
                    wire:navigate
                @endif>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        @if($parent->icon)
                            <i class="{{ $parent->icon }} text-slate-400 mr-3"></i>
                        @else
                            <i class="bi bi-folder text-slate-400 mr-3"></i>
                        @endif
                        <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                            {{ $parent->module_name }}
                        </span>
                    </div>
                    @if($childModules->isNotEmpty())
                        <div class="flex shrink-0 ml-2">
                            <svg class="lg:hidden lg:sidebar-expanded:inline w-3 h-3 shrink-0 ml-1 fill-current text-slate-400 transition-transform duration-200"
                                :class="open ? 'rotate-180' : 'rotate-0'"
                                viewBox="0 0 12 12">
                                <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                            </svg>
                        </div>
                    @endif
                </div>
            </a>

            {{-- Child modules --}}
            @if($childModules->isNotEmpty())
                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                    <ul class="pl-9 mt-1 transition-all duration-200 ease-in-out overflow-hidden"
                        x-show="open"
                        x-transition:enter="transition-all duration-200 ease-out"
                        x-transition:enter-start="opacity-0 max-h-0"
                        x-transition:enter-end="opacity-100 max-h-96"
                        x-transition:leave="transition-all duration-200 ease-in"
                        x-transition:leave-start="opacity-100 max-h-96"
                        x-transition:leave-end="opacity-0 max-h-0">
                        @foreach($childModules as $childModule)
                            @if($childModule->route)
                                @php
                                    $opensWfhWall = $childModule->route === '/employee-management/wfh-monitoring';
                                    $childHref = $opensWfhWall ? route('wfh-monitoring.wall') : route($childModule->route);
                                @endphp
                                <li class="mb-1 last:mb-0">
                                    <a class="block text-slate-400 hover:text-blue-500 transition-colors duration-150 truncate
                                        @if ($this->isRouteActive($childModule->route)) {{ '!text-blue-500' }} @endif"
                                        href="{{ $childHref }}"
                                        @if($opensWfhWall)
                                            target="_blank"
                                            rel="noopener"
                                        @endif>
                                        <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            {{ $childModule->module_name }}
                                        </span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
        </li>
    @endforeach

</div>
