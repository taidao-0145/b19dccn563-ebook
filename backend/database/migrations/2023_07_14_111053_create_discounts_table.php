<?php

use App\Enums\DiscountEnum;
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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedFloat('from_price')->default(0);
            $table->unsignedFloat('to_price')->default(0);
            $table->unsignedFloat('discount_amount');
            $table->enum('condition', [
                DiscountEnum::LESS,
                DiscountEnum::BETWEEN,
                DiscountEnum::GREATER,
            ])->default(DiscountEnum::LESS);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
};
