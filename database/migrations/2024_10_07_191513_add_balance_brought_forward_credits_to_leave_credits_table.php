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
            $table->decimal('vlbalance_brought_forward', 10, 3)->after('fl_claimed_credits')->nullable();
            $table->decimal('slbalance_brought_forward', 10, 3)->after('vlbalance_brought_forward')->nullable();
            $table->date('date_forwarded')->after('slbalance_brought_forward')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->dropColumn('vlbalance_brought_forward');
            $table->dropColumn('slbalance_brought_forward');
            $table->dropColumn('date_forwarded');
        });
    }
};
