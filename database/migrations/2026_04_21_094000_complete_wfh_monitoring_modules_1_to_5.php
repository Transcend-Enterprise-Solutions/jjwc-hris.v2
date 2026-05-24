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
        Schema::table('wfh_monitoring_sessions', function (Blueprint $table) {
            $table->timestamp('consented_at')->nullable()->after('screen_share_ended_at');
            $table->string('consent_version')->nullable()->after('consented_at');
            $table->timestamp('shift_end_at')->nullable()->after('ended_at');
            $table->unsignedInteger('grace_period_minutes')->default(15)->after('shift_end_at');
            $table->unsignedInteger('total_monitored_minutes')->default(0)->after('grace_period_minutes');
            $table->unsignedInteger('active_seconds')->default(0)->after('activity_count');
            $table->unsignedInteger('idle_seconds')->default(0)->after('active_seconds');
            $table->unsignedInteger('keystroke_count')->default(0)->after('idle_seconds');
            $table->unsignedInteger('mouse_activity_count')->default(0)->after('keystroke_count');
            $table->unsignedInteger('click_count')->default(0)->after('mouse_activity_count');
            $table->unsignedInteger('touch_count')->default(0)->after('click_count');
            $table->unsignedTinyInteger('activity_score')->default(0)->after('touch_count');
            $table->string('url_classification')->default('unclassified')->after('activity_score');
            $table->timestamp('last_focused_at')->nullable()->after('visibility_state');
            $table->timestamp('last_blurred_at')->nullable()->after('last_focused_at');
            $table->timestamp('afk_started_at')->nullable()->after('afk_threshold_minutes');
            $table->timestamp('afk_responded_at')->nullable()->after('afk_started_at');
            $table->string('afk_response')->nullable()->after('afk_responded_at');
            $table->boolean('afk_excused')->default(false)->after('afk_response');
            $table->text('afk_excuse_notes')->nullable()->after('afk_excused');
            $table->unsignedInteger('screenshot_interval_minutes')->default(5)->after('afk_excuse_notes');
            $table->unsignedInteger('location_interval_minutes')->default(30)->after('screenshot_interval_minutes');
            $table->boolean('screenshot_request_pending')->default(false)->after('location_interval_minutes');
            $table->timestamp('screenshot_requested_at')->nullable()->after('screenshot_request_pending');
            $table->foreignId('screenshot_requested_by')->nullable()->after('screenshot_requested_at')->constrained('users')->nullOnDelete();
            $table->string('field_location_label')->nullable()->after('screenshot_requested_by');
            $table->string('field_photo_path')->nullable()->after('field_location_label');
        });

        Schema::table('wfh_monitoring_location_pings', function (Blueprint $table) {
            $table->string('location_label')->nullable()->after('source');
            $table->string('photo_path')->nullable()->after('location_label');
        });

        Schema::table('wfh_monitoring_screenshots', function (Blueprint $table) {
            $table->string('capture_type')->default('periodic')->after('path');
            $table->boolean('flagged')->default(false)->after('captured_at');
            $table->text('flag_notes')->nullable()->after('flagged');
            $table->foreignId('flagged_by')->nullable()->after('flag_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('flagged_at')->nullable()->after('flagged_by');
        });

        Schema::create('wfh_monitoring_url_rules', function (Blueprint $table) {
            $table->id();
            $table->string('pattern');
            $table->string('classification')->default('productive');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        DB::table('wfh_monitoring_url_rules')->insert([
            [
                'pattern' => 'jjwc',
                'classification' => 'productive',
                'is_active' => true,
                'notes' => 'Default HRIS/domain productivity rule.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pattern' => 'docs.google.com',
                'classification' => 'productive',
                'is_active' => true,
                'notes' => 'Common browser-based document work.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pattern' => 'youtube.com',
                'classification' => 'non_productive',
                'is_active' => true,
                'notes' => 'Default non-work classification, editable by HR.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wfh_monitoring_url_rules');

        Schema::table('wfh_monitoring_screenshots', function (Blueprint $table) {
            $table->dropConstrainedForeignId('flagged_by');
            $table->dropColumn(['capture_type', 'flagged', 'flag_notes', 'flagged_at']);
        });

        Schema::table('wfh_monitoring_location_pings', function (Blueprint $table) {
            $table->dropColumn(['location_label', 'photo_path']);
        });

        Schema::table('wfh_monitoring_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('screenshot_requested_by');
            $table->dropColumn([
                'consented_at',
                'consent_version',
                'shift_end_at',
                'grace_period_minutes',
                'total_monitored_minutes',
                'active_seconds',
                'idle_seconds',
                'keystroke_count',
                'mouse_activity_count',
                'click_count',
                'touch_count',
                'activity_score',
                'url_classification',
                'last_focused_at',
                'last_blurred_at',
                'afk_started_at',
                'afk_responded_at',
                'afk_response',
                'afk_excused',
                'afk_excuse_notes',
                'screenshot_interval_minutes',
                'location_interval_minutes',
                'screenshot_request_pending',
                'screenshot_requested_at',
                'field_location_label',
                'field_photo_path',
            ]);
        });
    }
};
