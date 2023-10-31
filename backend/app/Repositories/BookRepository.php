<?php

namespace App\Repositories;

use App\Enums\BookOrderStatus;
use App\Enums\BookTypeEnum;
use App\Models\Book;
use App\Models\BookOrderItem;
use App\Models\User;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BookRepository extends BaseRepository implements BookRepositoryInterface
{
    /**
     * get model
     *
     * @return string
     */
    public function getModel(): string
    {
        return Book::class;
    }

    /**
     * Get books by filter
     *
     * @return LengthAwarePaginator
     */
    public function getBooksByFilter()
    {
        $perPage = request()->input('per_page') ?? config('const.paginate.default');
        $query = Book::query()
            ->where('is_limit_public', false)
            ->where('public_date', '<', now());

        $keyword = request()->input('keyword');
        if ($keyword) {
            $query->where(function (Builder $query) use ($keyword) {
                $query->where('isbn', 'LIKE', "%$keyword%")
                    ->orWhere('c_code', 'LIKE', "%$keyword%")
                    ->orWhere('name', 'LIKE', "%$keyword%")
                    ->orWhere('author_name', 'LIKE', "%$keyword%");
            });
        }

        return $this->filterBooks($query)
            ->paginate($perPage);
    }

    /**
     * Get purchased books by filter
     *
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function getPurchasedBooks(User $user)
    {
        $bookOrderItemIds = BookOrderItem::whereHas('bookOrder', function ($userQuery) use ($user) {
            $userQuery->whereIn('status', [
                BookOrderStatus::PAID,
                BookOrderStatus::SUCCESS,
            ])->where('user_id', $user->id);
        })->get()
            ->pluck('book_id')
            ->unique()
            ->toArray();
        $perPage = request()->input('per_page') ?? config('const.paginate.default');
        $query = Book::whereIn('id', $bookOrderItemIds)
            ->with('categories');

        return $this->filterBooks($query)
            ->paginate($perPage);
    }

    /**
     * Get related book by category
     *
     * @param Book $book
     * @return Book
     */
    public function getRelatedBook(Book $book)
    {
        $limit = request()->input('limit', config('const.paginate.limitRelatedBook'));
        $book->load([
            'categories.books' => function ($booksQuery) use ($book) {
                $booksQuery->where('books.id', '!=', $book->id)
                    ->where('books.is_limit_public', false)
                    ->where('books.public_date', '<', now());
            },
        ]);
        return $book->categories->pluck('books')
            ->collapse()
            ->shuffle()
            ->shift($limit);
    }

    /**
     * Get chapters of book
     *
     * @param Book $book
     * @return Collection
     */
    public function getChaptersOfBook(Book $book)
    {
        if ($book->book_type != BookTypeEnum::EBOOK_TYPE) {
            return collect([]);
        }

        $book->load('bookResources.bookChapters');

        return $book->bookResources
            ->pluck('bookChapters')
            ->collapse();
    }

    /**
     * Filter book
     *
     * @param Builder $bookQuery
     * @return Builder
     */
    protected function filterBooks(Builder $bookQuery)
    {
        $bookType = request()->input('book_type');
        if ($bookType) {
            $bookQuery->where('book_type', $bookType);
        }

        $category = request()->input('category');
        if ($category) {
            $bookQuery->whereHas('categories', function ($categoriesQuery) use ($category) {
                $categoriesQuery->where('categories.id', $category);
            });
        }

        return $bookQuery;
    }
}
