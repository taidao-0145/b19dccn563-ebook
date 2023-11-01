<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('book_order_id');
            $table->unsignedFloat('price')->default(0);
            $table->unsignedInteger('quantity');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('book_id')->references('id')->on('books');
            $table->foreign('book_order_id')->references('id')->on('book_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_order_items');
    }
};
