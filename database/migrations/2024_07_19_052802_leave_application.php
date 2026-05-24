<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('leave_application')) {
            Schema::create('leave_application', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedBigInteger('endorser1_id')->nullable();
                $table->foreign('endorser1_id')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedBigInteger('endorser2_id')->nullable();
                $table->foreign('endorser2_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('name');
                $table->string('office_or_department');
                $table->date('date_of_filing')->default(DB::raw('CURRENT_DATE'));
                $table->string('position');
                $table->decimal('salary', 8, 2);
                $table->string('type_of_leave');
                $table->text('details_of_leave')->nullable();
                $table->integer('number_of_days');
                $table->string('list_of_dates')->nullable();
                $table->string('commutation')->default('Not Requested');
                $table->string('file_name')->nullable();
                $table->string('file_path')->nullable();
                $table->string('status');
                $table->string('approved_dates')->nullable();
                $table->integer('approved_days')->nullable();
                $table->string('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leave_application')) {
            Schema::dropIfExists('leave_application');
        }
    }
};
