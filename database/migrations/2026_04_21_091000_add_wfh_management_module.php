<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $moduleId = DB::table('system_modules')
            ->where('route', '/employee-management/wfh-management')
            ->value('id');

        if (! $moduleId) {
            $moduleId = DB::table('system_modules')->insertGetId([
                'parent_module_id' => 1,
                'module_name' => 'WFH Management',
                'module_key' => 'wfh_management',
                'component_class' => 'App\\Livewire\\Admin\\WfhManagement',
                'route' => '/employee-management/wfh-management',
                'icon' => 'bi bi-display',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('admin_role_accesses')
            ->whereNotNull('modules')
            ->orderBy('id')
            ->get()
            ->each(function ($roleAccess) use ($moduleId) {
                $modules = array_filter(explode(',', $roleAccess->modules));

                if (! in_array('9', $modules, true) || in_array((string) $moduleId, $modules, true)) {
                    return;
                }

                $modules[] = (string) $moduleId;

                DB::table('admin_role_accesses')
                    ->where('id', $roleAccess->id)
                    ->update([
                        'modules' => implode(',', $modules),
                        'updated_at' => now(),
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $moduleId = DB::table('system_modules')
            ->where('route', '/employee-management/wfh-management')
            ->value('id');

        if (! $moduleId) {
            return;
        }

        DB::table('admin_role_accesses')
            ->whereNotNull('modules')
            ->orderBy('id')
            ->get()
            ->each(function ($roleAccess) use ($moduleId) {
                $modules = array_values(array_filter(
                    explode(',', $roleAccess->modules),
                    fn ($id) => $id !== (string) $moduleId
                ));

                DB::table('admin_role_accesses')
                    ->where('id', $roleAccess->id)
                    ->update([
                        'modules' => implode(',', $modules),
                        'updated_at' => now(),
                    ]);
            });

        DB::table('system_modules')->where('id', $moduleId)->delete();
    }
};
