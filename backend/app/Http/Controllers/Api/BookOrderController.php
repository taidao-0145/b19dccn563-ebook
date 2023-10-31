<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateBookOrderByCustomerRequest;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\BookOrderResource;
use App\Models\BookOrder;
use App\Services\BookOrderService;
use Illuminate\Http\JsonResponse;

class BookOrderController extends Controller
{
    public function __construct(
        protected BookOrderService $bookOrderService
    ) {
    }

    /**
     * Get Book Orders by customer
     *
     * @return JsonResponse
     */
    public function getBookOrders(): JsonResponse
    {
        $bookOrders = $this->bookOrderService->getBookOrders();

        return $this->responseApi(
            (new BaseResourceCollection($bookOrders))->data(BookOrderResource::collection($bookOrders))
        );
    }

    /**
     * Update Book Order by customer
     *
     * @return JsonResponse
     */
    public function updateBookOrder(BookOrder $bookOrder, UpdateBookOrderByCustomerRequest $request)
    {
        $updateData = $request->only([
            'receiver_name',
            'receiver_phone',
            'receiver_address',
        ]);
        $bookOrder = $this->bookOrderService->updateBookOrder($bookOrder, $updateData);

        return $this->responseApi($bookOrder);
    }
}
