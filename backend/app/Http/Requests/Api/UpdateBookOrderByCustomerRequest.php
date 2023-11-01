<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseAPIRequest;
use App\Rules\Api\StripePaymentMethodIdRule;

class UpdateBookOrderByCustomerRequest extends BaseAPIRequest
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
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string',
            'receiver_address' => 'required|string|max:500',
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
