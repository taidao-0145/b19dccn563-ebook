<?php

namespace App\Http\Requests\Api;

use App\Enums\BookTypeEnum;
use App\Http\Requests\BaseAPIRequest;
use Illuminate\Validation\Rule;

class CategoriesWithBooksRequest extends BaseAPIRequest
{
    /**
     * rulesPost
     * handle rule method post
     *
     * @return array
     */
    public function rulesGet(): array
    {
        return [
            'limit_book' => 'nullable|numeric|min:1',
            'book_type' => [
                'nullable',
                Rule::in([
                    BookTypeEnum::EBOOK_TYPE,
                    BookTypeEnum::BOOK_TYPE,
                    BookTypeEnum::LBOOK_TYPE,
                    BookTypeEnum::GBOOK_TYPE,
                ]),
            ]
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
