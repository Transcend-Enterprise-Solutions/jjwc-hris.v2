<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employees_dtr', function (Blueprint $table) {
            $table->string('ut')->nullable()->after('overtime');
        });
    }

    public function down()
    {
        Schema::table('employees_dtr', function (Blueprint $table) {
            $table->dropColumn('ut');
        });
    }
};
