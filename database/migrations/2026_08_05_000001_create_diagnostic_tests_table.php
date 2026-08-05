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
        Schema::create('diagnostic_tests', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('subtitle');
            $table->text('description');
            $table->enum('category', ['Heart', 'Blood', 'Thyroid', 'Energy', 'General']);
            $table->decimal('price', 10, 2);
            $table->integer('reports_in_hours');
            $table->string('sample_type');
            $table->boolean('fasting_required')->default(false);
            $table->boolean('is_package')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_tests');
    }
};
