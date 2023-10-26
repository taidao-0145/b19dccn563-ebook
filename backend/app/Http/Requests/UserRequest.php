<?php

namespace App\Http\Requests;

class UserRequest extends BaseAPIRequest
{
    /**
     * rulesPost
     * handle rule method post
     *
     * @return array
     */
    public function rulesPost(): array
    {
        return [];
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
