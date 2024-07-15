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
        Schema::create('residences', function (Blueprint $table) {
            $table->id();

            // This migration refers to VBO - verblijfsobject

            $table->unsignedBigInteger('identificatie')->unique();

            $table->string('status')->nullable();
            $table->string('oppervlakte')->nullable();
            $table->string('gebruiksdoel')->nullable();
            $table->boolean('geconstateerd')->nullable();
            $table->date('documentdatum')->nullable();
            $table->string('documentnummer')->nullable();

            $table->unsignedBigInteger('heeftAlsHoofdadres')->nullable()->comment('This is a reference to streets/NUM');
            $table->unsignedBigInteger('maaktDeelUitVan')->nullable()->comment('This is a reference to premise/PND');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residences');
    }
};
