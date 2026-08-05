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
        Schema::create('bookings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('diagnostic_test_id');
            $table->foreign('diagnostic_test_id')->references('id')->on('diagnostic_tests')->cascadeOnDelete();
            $table->string('partner_lab_id');
            $table->foreign('partner_lab_id')->references('id')->on('partner_labs')->cascadeOnDelete();
            $table->boolean('is_home_collection')->default(false);
            $table->date('date');
            $table->string('time_slot');
            $table->string('patient_name');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
