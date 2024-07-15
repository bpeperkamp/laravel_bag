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
        Schema::create('public_spaces', function (Blueprint $table) {
            $table->id();

            // This migration refers to OPR - openbare ruimte

            $table->unsignedBigInteger('identificatie')->unique();

            $table->string('naam')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->boolean('geconstateerd')->nullable();
            $table->date('documentdatum')->nullable();
            $table->string('documentnummer')->nullable();

            $table->unsignedBigInteger('ligtIn')->nullable()->comment('This is a reference to cities/WPL');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_spaces');
    }
};
