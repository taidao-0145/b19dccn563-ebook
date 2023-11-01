<?php

namespace App\Http\Requests\Api;

use App\Enums\PaymentMethodEnum;
use App\Http\Requests\BaseAPIRequest;
use App\Models\Book;
use App\Rules\Api\PaymentMethodRule;
use App\Rules\Api\RightDeferredFeeRule;
use App\Rules\Api\RightDiscountAmountRule;
use App\Rules\Api\RightDiscountPercentRule;
use App\Rules\Api\RightShippingFeeRule;

class ProcessPaymentWithoutCredentialRequest extends BaseAPIRequest
{
    /**
     * rulesPost
     * handle rule method post
     *
     * @return array
     */
    public function rulesPost(): array
    {
        $bookIds = collect($this->books)
            ->pluck('id')
            ->toArray();
        $booksInDB = Book::whereIn('id', $bookIds)->get();
        $totalPrice = 0;
        foreach ($booksInDB as $book) {
            $order = collect($this->books)->first(function ($orderTmp) use ($book) {
                return $orderTmp['id'] == $book->id;
            });
            $quantity = $order['quantity'] ?? 1;
            $totalPrice += $book->price * $quantity;
        }

        return [
            'payment_method' => [
                'required',
                new PaymentMethodRule(),
            ],
            'stripe_payment_method_id' => [
                'required_if:payment_method,' . PaymentMethodEnum::CREDIT_CARD,
                'string',
            ],
            'receiver_email' => 'required|string|email|max:255',
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string',
            'receiver_address' => 'required|string|max:500',
            'receiver_zipcode' => 'required|string|max:10',
            'memo' => 'nullable|string|max:500',
            'books' => 'required|array',
            'books.*.id' => 'required|exists:books,id',
            'books.*.quantity' => 'nullable|numeric|integer|min:1|max:999999',
            'shipping_fee' => [
                'required',
                'numeric',
                'max:999999999',
                new RightShippingFeeRule($this->books, $booksInDB),
            ],
            'deferred_fee' => [
                'required',
                'numeric',
                'max:999999999',
                new RightDeferredFeeRule($this->books, $booksInDB, $this->payment_method),
            ],
            'discount_percent' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                new RightDiscountPercentRule($totalPrice),
            ],
            'discount_amount' => [
                'required',
                'numeric',
                'max:999999999',
                new RightDiscountAmountRule($totalPrice, $this->discount_percent),
            ],
        ];
    }

    /**
     * Custom message for rules
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
