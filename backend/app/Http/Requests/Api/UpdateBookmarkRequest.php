<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseAPIRequest;
use App\Rules\Api\OnlyEBookRule;

class UpdateBookmarkRequest extends BaseAPIRequest
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
            'bookmarks' => [
                'array',
                new OnlyEBookRule($this->book),
            ],
            'bookmarks.*' => 'required|numeric|integer|min:0|max:' . $this->book->page_number,
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
