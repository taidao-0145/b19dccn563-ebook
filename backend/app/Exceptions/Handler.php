<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Throwable $e)
    {
        Log::critical($e);
        if (config('app.env') === 'local') {
            return parent::render($request, $e);
        }

        if ($e instanceof AuthenticationException) {
            $statusCode = 401;
            $errorCode = 'unauthorized';

            return $this->responseErrors($statusCode, $errorCode, '');
        }

        if ($e instanceof AuthorizationException) {
            $statusCode = 403;
            $errorCode = 'not_permission_access';

            return $this->responseErrors($statusCode, $errorCode, '');
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            $statusCode = 404;
            $errorCode = 'not_found';

            return $this->responseErrors($statusCode, $errorCode, '');
        }

        if ($e instanceof HttpResponseException) {
            $response = $e->getResponse();
            if ($response) {
                $data = $response->getContent();
                $statusCode = $response->getStatusCode();
                if ($data && $statusCode === 400) {
                    $data = json_decode($data);
                    $errorCode = $data->result->errorCode;

                    return $this->responseErrors($statusCode, $errorCode, '');
                }
            }
        }

        $statusCode = 500;
        $errorCode = 'internal_server_error';

        return $this->responseErrors($statusCode, $errorCode, '');
    }

    /**
     * Return response error
     *
     * @param string $statusCode : int
     * @param int    $errorCode  : string
     * @param string $fieldName  : string
     * @return \Illuminate\Http\JsonResponse
     */
    private function responseErrors($statusCode, $errorCode, $fieldName)
    {
        return response()->json([
            'status' => 'failure',
            'message' => __('httpStatusCode.messages.' . $statusCode),
            'result' => [
                'field' => $fieldName,
                'errorCode' => $errorCode,
                'errorMessage' => __('validation.errorCode.' . $errorCode),
            ],
        ], $statusCode);
    }
}
