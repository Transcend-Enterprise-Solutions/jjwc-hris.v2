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
        Schema::table('official_businesses', function (Blueprint $table) {
            $table->enum('duration', [
                'whole_day',
                'half_day',
                'am',
                'pm'
            ])->nullable()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('official_businesses', function (Blueprint $table) {
            $table->dropColumn(['duration']);
        });
    }
};
