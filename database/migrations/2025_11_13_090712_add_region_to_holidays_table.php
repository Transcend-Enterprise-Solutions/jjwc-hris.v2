<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('holidays')) {
            return;
        }

        if (! Schema::hasColumn('holidays', 'region_id')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->unsignedInteger('region_id')->nullable()->after('type');
            });
        } elseif (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE holidays MODIFY region_id INT UNSIGNED NULL AFTER type');
        }

        if ($this->shouldManageForeignKey() && ! $this->foreignExists()) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->foreign('region_id')->references('id')->on('philippine_regions')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('holidays') || ! Schema::hasColumn('holidays', 'region_id')) {
            return;
        }

        if ($this->shouldManageForeignKey() && $this->foreignExists()) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->dropForeign(['region_id']);
            });
        }

        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn('region_id');
        });
    }

    private function shouldManageForeignKey(): bool
    {
        return DB::connection()->getDriverName() === 'mysql' && Schema::hasTable('philippine_regions');
    }

    private function foreignExists(): bool
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'holidays')
            ->where('COLUMN_NAME', 'region_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();
    }
};
