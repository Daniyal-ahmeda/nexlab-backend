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
        Schema::create('partner_labs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->decimal('rating', 3, 1);
            $table->integer('reviews_count');
            $table->string('address');
            $table->string('phone');
            $table->string('hours');
            $table->boolean('has_home_collection')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_labs');
    }
};
