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
        Schema::table('monetization_request', function (Blueprint $table) {
            // Add the new column `date_approved` as a nullable date
            $table->date('date_approved')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monetization_request', function (Blueprint $table) {
            // Drop the `date_approved` column if the migration is rolled back
            $table->dropColumn('date_approved');
        });
    }
};