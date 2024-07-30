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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('rent_id')->unsigned()->nullable();
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('driver_id')->unsigned()->nullable();
            $table->integer('price')->default(0);
            $table->integer('driver_price')->default(0);
            $table->integer('total')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('rent_id')->references('id')->on('rents');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('driver_id')->references('id')->on('drivers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
