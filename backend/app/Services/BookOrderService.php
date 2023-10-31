<?php

namespace App\Services;

use App\Enums\BookOrderStatus;
use App\Enums\BookTypeEnum;
use App\Enums\ShippingStatusEnum;
use App\Models\Book;
use App\Models\BookOrder;
use App\Repositories\Interfaces\BookOrderRepositoryInterface as BookOrderRepository;
use App\Repositories\Interfaces\BookRepositoryInterface as BookRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookOrderService
{
    public function __construct(
        protected BookRepository $bookRepository,
        protected BookOrderRepository $bookOrderRepository
    ) {
    }

    /**
     * Get Book Orders by customer
     *
     * @return Collection
     */
    public function getBookOrders()
    {
        $user = Auth::user();

        return $this->bookOrderRepository->getBookOrders($user);
    }

    /**
     * Create Book Orders
     *
     * @param Request $request
     * @return Collection
     */
    public function createBookOrders(Request $request)
    {
        $user = Auth::user();
        $bookOrderData = [
            'code' => $this->makeCode(),
            'receiver_email' => $user ? $user->email : $request->receiver_email,
            'receiver_name' => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'receiver_address' => $request->receiver_address,
            'receiver_zipcode' => $request->receiver_zipcode,
            'discount_percent' => $request->discount_percent,
            'memo' => $request->memo,
            'payment_method' => $request->payment_method,
            'status' => BookOrderStatus::IN_PAYMENT,
            'shipping_status' => ShippingStatusEnum::NONE,
            'user_id' => $user ? $user->id : null,
        ];
        $bookIds = collect($request->books)
            ->pluck('id')
            ->toArray();
        $books = $this->bookRepository->whereIn('id', $bookIds)->get();

        // Calculate master order
        $masterOrder = $this->generateMasterOrder($request, $books);

        // Calculate book order
        $bookOrder = $this->generateBookOrder($request, $books, $bookOrderData);

        // Calculate digital book order (EBOOK|LBOOK)
        $this->generateDigitalBookOrder($request, $books, $masterOrder, $bookOrder, $bookOrderData);

        return $this->bookOrderRepository->getBookOrdersByCode($bookOrderData['code']);
    }

    /**
     * Update Book Order
     *
     * @param BookOrder $bookOrder
     * @param array $updateData
     * @return BookOrder|bool
     */
    public function updateBookOrder(BookOrder $bookOrder, array $updateData)
    {
        return $this->bookOrderRepository->update($bookOrder->id, $updateData);
    }

    /**
     * Generate master order
     *
     * @param Request    $request
     * @param Collection $books
     * @return array(totalBooks,totalBooksAfterDiscount,discount,discountPercent,shippingFee,totalPrice)
     */
    protected function generateMasterOrder(Request $request, Collection $books)
    {
        $totalBooks = 0;
        foreach ($books as $book) {
            $order = collect($request->books)->first(function ($orderTmp) use ($book) {
                return $orderTmp['id'] == $book->id;
            });
            $quantity = $order['quantity'] ?? 1;
            $totalBooks += $book->price * $quantity;
        }
        $discount = $request->discount_amount ?? 0;
        $totalBooksAfterDiscount = $totalBooks - $discount;
        $shippingFee = $request->shipping_fee ?? 0;
        $deferredFee = $request->deferred_fee ?? 0;
        $totalPrice = $totalBooks - $discount + $shippingFee + $deferredFee;

        return [
            'totalBooks' => $totalBooks,
            'totalBooksAfterDiscount' => $totalBooksAfterDiscount,
            'discount' => $discount,
            'discountPercent' => $request->discount_percent,
            'shippingFee' => $shippingFee,
            'deferredFee' => $deferredFee,
            'totalPrice' => $totalPrice,
        ];
    }

    /**
     * Generate book order
     *
     * @param Request    $request
     * @param Collection $books
     * @param array      $bookOrderData
     * @return array(totalBooks,totalBooksAfterDiscount,discount,discountPercent,shippingFee,totalPrice)
     */
    protected function generateBookOrder(Request $request, Collection $books, array $bookOrderData)
    {
        $filterBooks = $books->filter(function ($bookTmp) {
            return $bookTmp->book_type === BookTypeEnum::BOOK_TYPE;
        });

        $totalBooks = 0;
        foreach ($filterBooks as $book) {
            $order = collect($request->books)->first(function ($orderTmp) use ($book) {
                return $orderTmp['id'] == $book->id;
            });
            $quantity = $order['quantity'] ?? 1;
            $totalBooks += $book->price * $quantity;
        }

        $discountPercent = $request->discount_percent ?? 0;
        $totalBooksAfterDiscount = round(($totalBooks / 100 * (100 - $discountPercent)));
        $discount = $totalBooks - $totalBooksAfterDiscount;
        $shippingFee = $request->shipping_fee ?? 0;
        $deferredFee = $request->deferred_fee ?? 0;
        $totalPrice = $totalBooks - $discount + $shippingFee + $deferredFee;

        // Save DB
        $bookOrderData['total_price'] = $totalPrice;
        $bookOrderData['shipping_fee'] = $shippingFee;
        $bookOrderData['deferred_fee'] = $deferredFee;
        $bookOrderData['discount_percent'] = $discountPercent;
        $bookOrderData['discount_amount'] = $discount;
        $this->bookOrderRepository->createBookOrder($request->books, $filterBooks, $bookOrderData);

        return [
            'totalBooks' => $totalBooks,
            'totalBooksAfterDiscount' => $totalBooksAfterDiscount,
            'discount' => $discount,
            'discountPercent' => $request->discount_percent,
            'shippingFee' => $shippingFee,
            'deferredFee' => $deferredFee,
            'totalPrice' => $totalPrice,
        ];
    }

    /**
     * Generate digital book order
     *
     * @param Request    $request
     * @param Collection $books
     * @param array      $masterOrder
     * @param array      $bookOrder
     * @param array      $bookOrderData
     * @return array(totalBooks,totalBooksAfterDiscount,discount,discountPercent,shippingFee,totalPrice)
     */
    protected function generateDigitalBookOrder(
        Request $request,
        Collection $books,
        array $masterOrder,
        array $bookOrder,
        array $bookOrderData
    ) {
        $filterBooks = $books->filter(function ($bookTmp) {
            return $bookTmp->book_type !== BookTypeEnum::BOOK_TYPE;
        });

        $totalBooks = 0;
        foreach ($filterBooks as $book) {
            $order = collect($request->books)->first(function ($orderTmp) use ($book) {
                return $orderTmp['id'] == $book->id;
            });
            $quantity = $order['quantity'] ?? 1;
            $totalBooks += $book->price * $quantity;
        }

        $totalBooksAfterDiscount = $masterOrder['totalBooksAfterDiscount'] - $bookOrder['totalBooksAfterDiscount'];
        $discount = $totalBooks - $totalBooksAfterDiscount;
        $discountPercent = $request->discount_percent ?? 0;
        $shippingFee = 0;
        $deferredFee = 0;
        $totalPrice = $totalBooks - $discount;

        // Save DB
        $bookOrderData['total_price'] = $totalPrice;
        $bookOrderData['shipping_fee'] = $shippingFee;
        $bookOrderData['discount_percent'] = $discountPercent;
        $bookOrderData['discount_amount'] = $discount;
        $bookOrderData['deferred_fee'] = $deferredFee;
        $this->bookOrderRepository->createBookOrder($request->books, $filterBooks, $bookOrderData);

        return [
            'totalBooks' => $totalBooks,
            'totalBooksAfterDiscount' => $totalBooksAfterDiscount,
            'discount' => $discount,
            'discountPercent' => $discountPercent,
            'shippingFee' => $shippingFee,
            'deferredFee' => $deferredFee,
            'totalPrice' => $totalPrice,
        ];
    }

    /**
     * Make code for book order
     *
     * @return string
     */
    protected function makeCode(): string
    {
        while (true) {
            $code = strtoupper(Str::random(config('const.LENGTH_OF_RANDOM_CODE')));
            if (!$this->bookOrderRepository->isExist('code', $code)) {
                break;
            }
        }

        return $code;
    }
}
