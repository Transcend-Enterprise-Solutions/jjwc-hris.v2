<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->decimal('vl_total_credits', 10, 3)->nullable()->after('user_id');
            $table->decimal('sl_total_credits', 10, 3)->nullable()->after('vl_total_credits');
            $table->decimal('spl_total_credits', 10, 3)->nullable()->after('sl_total_credits');
        });
    }

    public function down()
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->dropColumn(['vl_total_credits', 'sl_total_credits', 'spl_total_credits']);
        });
    }

};
