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
        Schema::table('user_data', function (Blueprint $table) {
            $table->string('umid', 50)->after('blood_type')->nullable();
            $table->string('philsys', 50)->after('philhealth')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_data', function (Blueprint $table) {
            $table->dropColumn([
                'umid',
                'philsys'
            ]);
        });
    }
};
