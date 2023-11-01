<?php

namespace App\Http\Middleware\Api;

use App\Services\BookService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifiedPaidBook
{
    public function __construct(protected BookService $bookService)
    {
        //
    }

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
        if ($book) {
            $isBookWasPaid = $this->bookService->isBookWasPaid($book);
            if ($isBookWasPaid) {
                return $next($request);
            }
        }

        return responseError(
            [
                [
                    'field' => __('book'),
                    'errorCode' => 'book_required_paid',
                    'errorMessage' => __('book_required_paid'),
                ],
            ],
            config('const.httpStatusCode.http_403'),
            __('httpStatusCode.messages.' . config('const.httpStatusCode.http_403'))
        );
    }
}
