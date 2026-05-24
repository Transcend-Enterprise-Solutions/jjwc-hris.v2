<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wfh_monitoring_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('wfh_monitoring_sessions', 'online_seconds')) {
                $table->unsignedInteger('online_seconds')->default(0)->after('total_monitored_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wfh_monitoring_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('wfh_monitoring_sessions', 'online_seconds')) {
                $table->dropColumn('online_seconds');
            }
        });
    }
};
