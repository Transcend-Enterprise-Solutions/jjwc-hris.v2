<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leave_credits_calculation', function (Blueprint $table) {
            // Add the new column after 'late_in_credits'
            $table->decimal('latest_vl_credits', 10, 3)->nullable()->after('late_in_credits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_credits_calculation', function (Blueprint $table) {
            // Drop the column if the migration is rolled back
            $table->dropColumn('latest_vl_credits');
        });
    }
};