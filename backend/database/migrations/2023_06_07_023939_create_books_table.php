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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn');
            $table->string('name');
            $table->string('description');
            $table->string('thumbnail');
            $table->string('author_name');
            $table->integer('page_number');
            $table->dateTime('relase_date');
            $table->integer('inventory_number');
            $table->float('price');
            $table->string('sample_path');
            $table->enum('book_type', ['LBOOK', 'EBOOK', 'BOOK']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
