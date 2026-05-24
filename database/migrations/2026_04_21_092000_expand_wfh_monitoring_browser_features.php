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
            $table->boolean('screen_share_active')->default(false)->after('work_status');
            $table->timestamp('screen_share_started_at')->nullable()->after('screen_share_active');
            $table->timestamp('screen_share_ended_at')->nullable()->after('screen_share_started_at');
            $table->decimal('last_latitude', 10, 7)->nullable()->after('last_activity_at');
            $table->decimal('last_longitude', 10, 7)->nullable()->after('last_latitude');
            $table->decimal('last_location_accuracy', 10, 2)->nullable()->after('last_longitude');
            $table->decimal('last_geofence_distance', 10, 2)->nullable()->after('last_location_accuracy');
            $table->string('geofence_status')->nullable()->after('last_geofence_distance');
            $table->string('visibility_state')->nullable()->after('geofence_status');
            $table->timestamp('offline_alerted_at')->nullable()->after('visibility_state');
            $table->timestamp('tamper_alerted_at')->nullable()->after('offline_alerted_at');
            $table->string('device_platform')->nullable()->after('tamper_alerted_at');
            $table->boolean('is_pwa')->default(false)->after('device_platform');
            $table->text('user_agent')->nullable()->after('is_pwa');
        });

        Schema::create('wfh_monitoring_location_pings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wfh_monitoring_session_id')->constrained('wfh_monitoring_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('accuracy', 10, 2)->nullable();
            $table->decimal('distance_from_geofence', 10, 2)->nullable();
            $table->string('geofence_status')->default('unknown');
            $table->string('source')->default('browser');
            $table->timestamp('occurred_at');
            $table->timestamps();
        });

        Schema::create('wfh_monitoring_screenshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wfh_monitoring_session_id')->constrained('wfh_monitoring_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('mime_type')->default('image/jpeg');
            $table->unsignedInteger('size_bytes')->default(0);
            $table->timestamp('captured_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wfh_monitoring_screenshots');
        Schema::dropIfExists('wfh_monitoring_location_pings');

        Schema::table('wfh_monitoring_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'screen_share_active',
                'screen_share_started_at',
                'screen_share_ended_at',
                'last_latitude',
                'last_longitude',
                'last_location_accuracy',
                'last_geofence_distance',
                'geofence_status',
                'visibility_state',
                'offline_alerted_at',
                'tamper_alerted_at',
                'device_platform',
                'is_pwa',
                'user_agent',
            ]);
        });
    }
};
