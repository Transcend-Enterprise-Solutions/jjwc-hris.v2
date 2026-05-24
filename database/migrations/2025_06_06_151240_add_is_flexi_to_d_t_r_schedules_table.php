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
        Schema::table('dtrschedules', function (Blueprint $table) {
            $table->boolean('is_flexi')
                  ->default(false)
                  ->after('end_date')
                  ->comment('Flag for flexible schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('dtrschedules', function (Blueprint $table) {
            $table->dropColumn('is_flexi');
        });
    }
};
