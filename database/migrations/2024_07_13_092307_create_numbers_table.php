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
        Schema::create('numbers', function (Blueprint $table) {
            $table->id();

            // This migration refers to NUM - numbers

            $table->unsignedBigInteger('identificatie')->unique();

            $table->string('postcode')->nullable();
            $table->integer('nummer')->nullable();
            $table->string('huisletter')->nullable();

            $table->unsignedBigInteger('ligtAan')->nullable()->comment('This is a reference to public spaces/OPR');
            $table->unsignedBigInteger('ligtIn')->nullable()->comment('This is a reference to cities/WPL');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numbers');
    }
};
