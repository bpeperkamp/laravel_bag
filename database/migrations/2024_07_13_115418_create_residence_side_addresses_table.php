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
        Schema::create('residence_side_addresses', function (Blueprint $table) {
            $table->id();

            // This migration refers to to VBO - verblijfsobject and connects it to a number NUM

            $table->unsignedBigInteger('residence_identificatie')->comment('This is a reference to residence/VBO');

            $table->unsignedBigInteger('number_identificatie')->comment('This is a reference to number/NUM');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residence_side_addresses');
    }
};
