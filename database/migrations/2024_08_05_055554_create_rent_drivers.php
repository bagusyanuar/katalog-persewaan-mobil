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
        Schema::create('rent_drivers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rent_id')->unsigned();
            $table->bigInteger('driver_id')->unsigned();
            $table->integer('price')->default(0);
            $table->timestamps();
            $table->foreign('rent_id')->references('id')->on('rents');
            $table->foreign('driver_id')->references('id')->on('drivers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_drivers');
    }
};
