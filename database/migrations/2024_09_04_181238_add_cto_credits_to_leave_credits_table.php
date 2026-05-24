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
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->decimal('cto_total_credits', 10, 3)->nullable()->after('spl_claimed_credits');
            $table->decimal('cto_claimable_credits', 10, 3)->nullable()->after('cto_total_credits');
            $table->decimal('cto_claimed_credits', 10, 3)->nullable()->after('cto_claimable_credits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->dropColumn('cto_total_credits');
            $table->dropColumn('cto_claimable_credits');
            $table->dropColumn('cto_claimed_credits');
        });
    }
};
