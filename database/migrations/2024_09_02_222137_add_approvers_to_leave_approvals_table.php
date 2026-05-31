<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('leave_approvals')) {
            return;
        }

        $this->ensureApproverColumn('first_approver', 'application_id');
        $this->ensureApproverColumn('second_approver', 'first_approver');
        $this->ensureApproverColumn('third_approver', 'second_approver');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('leave_approvals')) {
            return;
        }

        foreach (['third_approver', 'second_approver', 'first_approver'] as $column) {
            if (! Schema::hasColumn('leave_approvals', $column)) {
                continue;
            }

            if ($this->foreignExists($column)) {
                Schema::table('leave_approvals', function (Blueprint $table) use ($column) {
                    $table->dropForeign([$column]);
                });
            }

            Schema::table('leave_approvals', function (Blueprint $table) use ($column) {
                $table->dropColumn($column);
            });
        }
    }

    private function ensureApproverColumn(string $column, string $after): void
    {
        if (! Schema::hasColumn('leave_approvals', $column)) {
            Schema::table('leave_approvals', function (Blueprint $table) use ($column, $after) {
                $table->unsignedBigInteger($column)->nullable()->after($after);
            });
        }

        if (! $this->foreignExists($column)) {
            Schema::table('leave_approvals', function (Blueprint $table) use ($column) {
                $table->foreign($column)->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    private function foreignExists(string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return true;
        }

        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'leave_approvals')
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();
    }
};
