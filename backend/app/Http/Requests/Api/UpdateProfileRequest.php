<?php

namespace App\Http\Requests\Api;

use App\Enums\GenderEnum;
use App\Http\Requests\BaseAPIRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends BaseAPIRequest
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
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'address' => 'nullable|string|max:500',
            'gender' => [
                'nullable',
                Rule::in([
                    GenderEnum::MALE,
                    GenderEnum::FEMALE,
                ]),
            ],
            'date_of_birth' => 'nullable|string|date',
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
