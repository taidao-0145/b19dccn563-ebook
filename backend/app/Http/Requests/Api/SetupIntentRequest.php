<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseAPIRequest;
use Illuminate\Support\Facades\Auth;

class SetupIntentRequest extends BaseAPIRequest
{
    /**
     * rulesPost
     * handle rule method post
     *
     * @return array
     */
    public function rulesPost(): array
    {
        $user = auth('api')->user();

        return $user ? [] : [
            'email' => 'required|email|max:255',
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
