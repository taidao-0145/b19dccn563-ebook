<?php

namespace App\Repositories;

use App\Models\BookOrder;
use App\Models\BookOrderItem;
use App\Models\User;
use App\Repositories\Interfaces\BookOrderRepositoryInterface;
use Illuminate\Support\Collection;

class BookOrderRepository extends BaseRepository implements BookOrderRepositoryInterface
{
    /**
     * get model
     *
     * @return string
     */
    public function getModel(): string
    {
        return BookOrder::class;
    }

    /**
     * Get Book Orders by customer
     *
     * @param User $user
     * @return Collection
     */
    public function getBookOrders(User $user)
    {
        $perPage = request()->input('per_page') ?? config('const.paginate.default');

        return $this->_model->where('user_id', $user->id)
            ->with(['bookOrderItems.book' => function ($query) {
                $query->withTrashed();
            }])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Get Book Orders by code
     *
     * @param string $code
     * @return Collection
     */
    public function getBookOrdersByCode(string $code)
    {
        return $this->_model->where('code', $code)
            ->with('bookOrderItems.book')
            ->get();
    }

    /**
     * Create Book Order
     *
     * @param array      $orders
     * @param Collection $books
     * @param array      $bookOrderData
     * @return BookOrder|null
     */
    public function createBookOrder(array $orders, Collection $books, array $bookOrderData)
    {
        if (!$bookOrderData['total_price']) {
            return null;
        }

        $bookOrderData['hash_id'] = $this->makeHashId();
        $bookOrder = $this->_model->create($bookOrderData);

        foreach ($books as $book) {
            $order = collect($orders)->first(function ($orderTmp) use ($book) {
                return $orderTmp['id'] == $book->id;
            });
            $quantity = $order['quantity'] ?? 1;

            BookOrderItem::create([
                'book_id' => $book->id,
                'book_order_id' => $bookOrder->id,
                'price' => $book->price,
                'quantity' => $quantity,
            ]);
        }

        return $bookOrder;
    }
}
