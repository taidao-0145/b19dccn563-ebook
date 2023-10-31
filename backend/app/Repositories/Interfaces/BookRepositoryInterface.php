<?php

namespace App\Repositories\Interfaces;

use App\Models\Book;
use App\Models\User;

interface BookRepositoryInterface
{
    public function getBooksByFilter();
    public function getPurchasedBooks(User $user);
    public function getRelatedBook(Book $book);
    public function getChaptersOfBook(Book $book);
}
