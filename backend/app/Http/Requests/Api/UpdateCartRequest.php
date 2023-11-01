<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseAPIRequest;
use App\Rules\Api\StripePaymentMethodIdRule;

class UpdateCartRequest extends BaseAPIRequest
{
    /**
     * rulesPost
     * handle rule method post
     *
     * @return array
     */
    public function rulesPost(): array
    {
        return [
            'book_carts' => 'array',
            'book_carts.*.id' => 'required|exists:books,id',
            'book_carts.*.quantity' => 'required|numeric|integer|min:1|max:999999',
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
