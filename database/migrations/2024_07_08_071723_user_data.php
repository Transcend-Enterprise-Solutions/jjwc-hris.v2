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
        if (!Schema::hasTable('user_data')) {
            Schema::create('user_data', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('surname');
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('name_extension')->nullable();
                $table->string('email', 50);
                $table->date('date_of_birth')->format('F d Y');
                $table->string('place_of_birth');
                $table->string('sex');
                $table->string('civil_status');
                $table->string('citizenship');
                $table->string('dual_citizenship_type')->nullable();
                $table->string('dual_citizenship_country')->nullable();
                $table->float('height');
                $table->float('weight');
                $table->string('blood_type', 5)->nullable();
                $table->string('gsis', 50)->nullable();
                $table->string('pagibig', 50)->nullable();
                $table->string('philhealth', 50)->nullable();
                $table->string('sss', 50)->nullable();
                $table->string('tin', 50)->nullable();
                $table->string('agency_employee_no', 50)->nullable();
                $table->string('tel_number')->nullable();
                $table->string('mobile_number');
                $table->string('permanent_selectedProvince', 200);
                $table->string('permanent_selectedCity', 200);
                $table->string('permanent_selectedBarangay', 200);
                $table->string('p_house_street', 200);
                $table->string('permanent_selectedZipcode', 200);
                $table->string('residential_selectedProvince', 200);
                $table->string('residential_selectedCity', 200);
                $table->string('residential_selectedBarangay', 200);
                $table->string('r_house_street', 200);
                $table->string('residential_selectedZipcode', 200);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_data')) {
            Schema::dropIfExists('user_data');
        }
    }
};
