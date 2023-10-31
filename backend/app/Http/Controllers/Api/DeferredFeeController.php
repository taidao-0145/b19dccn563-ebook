<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeferredFeeResource;
use App\Services\DeferredFeeService;
use Illuminate\Http\JsonResponse;

class DeferredFeeController extends Controller
{
    public function __construct(
        protected DeferredFeeService $deferredFeeService
    ) {
    }

    /**
     * Get deferred fees
     *
     * @return JsonResponse
     */
    public function getDeferredFees(): JsonResponse
    {
        $deferredFees = $this->deferredFeeService->getDeferredFees();

        return $this->responseApi(
            [
                'deferred_fees' => DeferredFeeResource::collection($deferredFees),
            ]
        );
    }
}
