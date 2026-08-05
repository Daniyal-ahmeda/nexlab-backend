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
        Schema::create('biomarkers', function (Blueprint $table) {
            $table->id();
            $table->string('test_result_id');
            $table->foreign('test_result_id')->references('id')->on('test_results')->cascadeOnDelete();
            $table->string('name');
            $table->string('value');
            $table->string('unit');
            $table->string('reference_range');
            $table->enum('status', ['Normal', 'Low', 'High', 'Borderline']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biomarkers');
    }
};
