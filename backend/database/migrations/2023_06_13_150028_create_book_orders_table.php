<?php

use App\Enums\BookOrderStatus;
use App\Enums\ShippingStatusEnum;
use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_orders', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->unique();
            $table->string('code');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedFloat('total_price')->default(0);
            $table->unsignedFloat('shipping_fee')->default(0);
            $table->unsignedFloat('deferred_fee')->default(0);
            $table->unsignedFloat('discount_amount')->default(0);
            $table->unsignedInteger('discount_percent')->default(0);
            $table->string('receiver_email')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_address', 500)->nullable();
            $table->string('receiver_zipcode')->nullable();
            $table->string('memo', 500)->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->enum('payment_method', [
                PaymentMethodEnum::CREDIT_CARD,
                PaymentMethodEnum::BANK_TRANSFER,
                PaymentMethodEnum::POSTAL_DELIVERY,
                PaymentMethodEnum::DIRECT_PAYMENT,
            ])->default(PaymentMethodEnum::CREDIT_CARD);
            $table->enum('shipping_status', [
                ShippingStatusEnum::NONE,
                ShippingStatusEnum::SHIPPING,
                ShippingStatusEnum::SHIPPED,
            ])->default(ShippingStatusEnum::NONE);
            $table->enum('status', [
                BookOrderStatus::CREATED,
                BookOrderStatus::PAYMENT_ERROR,
                BookOrderStatus::IN_PAYMENT,
                BookOrderStatus::PAID,
                BookOrderStatus::IN_PROGRESS_DELIVERY,
                BookOrderStatus::SUCCESS,
                BookOrderStatus::CANCEL,
            ])->default(BookOrderStatus::CREATED);
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
        Schema::dropIfExists('book_orders');
    }
};
