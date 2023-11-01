<?php

namespace App\Http\Middleware\Api;

use App\Enums\BookTypeEnum;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IsEBookOrGBook
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $book = $request->book;
        if ($book && ($book->book_type === BookTypeEnum::EBOOK_TYPE || $book->book_type === BookTypeEnum::GBOOK_TYPE)) {
            return $next($request);
        }

        return responseError(
            [
                [
                    'field' => __('book'),
                    'errorCode' => 'ebook_gbook_only',
                    'errorMessage' => __('ebook_gbook_only'),
                ],
            ],
            config('const.httpStatusCode.http_403'),
            __('httpStatusCode.messages.' . config('const.httpStatusCode.http_403'))
        );
    }
}
