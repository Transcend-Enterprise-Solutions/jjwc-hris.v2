<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreditsInputtedToLeaveCreditsTable extends Migration
{
    public function up()
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->boolean('credits_inputted')->default(false)->after('credits_transferred');
        });
    }

    public function down()
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->dropColumn('credits_inputted');
        });
    }
}
