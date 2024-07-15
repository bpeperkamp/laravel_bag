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
        Schema::create('premises', function (Blueprint $table) {
            $table->id();

            // This migration refers to to PND - pand

            $table->unsignedBigInteger('identificatie')->unique();

            $table->string('status')->nullable();
            $table->boolean('geconstateerd')->nullable();
            $table->date('oorspronkelijkBouwjaar')->nullable();
            $table->date('documentdatum')->nullable();
            $table->string('documentnummer')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premises');
    }
};
