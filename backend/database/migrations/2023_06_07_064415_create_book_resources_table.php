<?php

use App\Enums\BookResourceStatusEnum;
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
        Schema::create('book_resources', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('path')->nullable();
            $table->string('local_path')->nullable();
            $table->unsignedInteger('duration')->default(0);
            $table->string('thumbnail')->nullable();
            $table->enum('status', [
                BookResourceStatusEnum::CREATED,
                BookResourceStatusEnum::IS_CONVERTING,
                BookResourceStatusEnum::ERROR,
                BookResourceStatusEnum::DONE,
            ])->default(BookResourceStatusEnum::CREATED);
            $table->unsignedBigInteger('book_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('book_id')->references('id')->on('books');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_resources');
    }
};
