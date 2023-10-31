<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Support\Collection;

interface BookOrderRepositoryInterface
{
    public function getBookOrders(User $user);
    public function createBookOrder(array $orders, Collection $books, array $bookOrderData);
    public function getBookOrdersByCode(string $code);
}
