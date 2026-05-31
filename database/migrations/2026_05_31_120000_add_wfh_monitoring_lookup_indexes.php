<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wfh_monitoring_sessions', function (Blueprint $table) {
            $table->index(['started_at', 'last_activity_at'], 'wfh_sessions_started_activity_idx');
            $table->index(['user_id', 'started_at'], 'wfh_sessions_user_started_idx');
        });

        Schema::table('wfh_monitoring_location_pings', function (Blueprint $table) {
            $table->index(['wfh_monitoring_session_id', 'occurred_at'], 'wfh_location_session_occurred_idx');
        });

        Schema::table('wfh_monitoring_screenshots', function (Blueprint $table) {
            $table->index(['wfh_monitoring_session_id', 'captured_at'], 'wfh_screenshot_session_captured_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wfh_monitoring_screenshots', function (Blueprint $table) {
            $table->dropIndex('wfh_screenshot_session_captured_idx');
        });

        Schema::table('wfh_monitoring_location_pings', function (Blueprint $table) {
            $table->dropIndex('wfh_location_session_occurred_idx');
        });

        Schema::table('wfh_monitoring_sessions', function (Blueprint $table) {
            $table->dropIndex('wfh_sessions_user_started_idx');
            $table->dropIndex('wfh_sessions_started_activity_idx');
        });
    }
};
