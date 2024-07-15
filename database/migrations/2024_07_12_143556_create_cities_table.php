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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();

            // This migration refers to WPL - woonplaats
            $table->unsignedBigInteger('identificatie')->unique();
            $table->string('naam')->nullable();

            // There are some strange validity dates in the XML file. (Check Weesp for example, 3 entries, two are valid, one is outdated. There is only one Weesp)
            // $table->date('beginGeldigheid')->nullable();
            // $table->date('eindGeldigheid')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
