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
            $table->dropColumn('total_credits');
        });
    }

    public function down()
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->decimal('total_credits', 10, 3)->nullable();
        });
    }

};
