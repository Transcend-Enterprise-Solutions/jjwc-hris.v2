<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlClaimableCreditsToLeaveCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->decimal('fl_claimable_credits', 10, 3)->nullable()->after('cto_claimed_credits');
            $table->decimal('fl_claimed_credits', 10, 3)->nullable()->after('fl_claimable_credits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->dropColumn('fl_claimable_credits');
            $table->dropColumn('fl_claimed_credits');
        });
    }
}
