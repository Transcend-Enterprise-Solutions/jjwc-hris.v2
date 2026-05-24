<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees_dtr', function (Blueprint $table) {


            // Add new time tracking columns
            $table->string('updated_by', 255)->nullable()->default(null);
            $table->string('up_morning_in', 255)->nullable()->default(null);
            $table->string('up_morning_out', 255)->nullable()->default(null);
            $table->string('up_afternoon_in', 255)->nullable()->default(null);
            $table->string('up_afternoon_out', 255)->nullable()->default(null);
            $table->string('up_late', 255)->nullable()->default(null);
            $table->string('up_ut', 255)->nullable()->default(null);
            $table->string('up_ot', 255)->nullable()->default(null);
            $table->string('up_total_hours_rendered', 255)->nullable()->default(null);
        });
    }

    public function down(): void
    {
        Schema::table('employees_dtr', function (Blueprint $table) {

            // Drop the added columns
            $table->dropColumn([
                'updated_by',
                'up_morning_in',
                'up_morning_out',
                'up_afternoon_in',
                'up_afternoon_out',
                'up_late',
                'up_ut',
                'up_ot',
                'up_total_hours_rendered'
            ]);
        });
    }
};
