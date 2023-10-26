<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Api data response from service
     *
     * @param object $result        : service result
     * @param int    $statusCode    : code of response
     * @param int    $message       : message
     * @return JsonResponse
     */
    public function responseApi($result, $statusCode = 200, $message = '')
    {
        return response()->json([
            'status' => ($statusCode == 200) ? 'success' : 'failure',
            'message' => $message ?? __('httpStatusCode.messages.' . $statusCode),
            'result' => is_null($result) ? [] : $result,
        ], $statusCode);
    }
}
