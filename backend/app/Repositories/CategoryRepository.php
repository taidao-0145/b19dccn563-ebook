<?php

namespace App\Repositories;

use App\Enums\BookOrderStatus;
use App\Models\BookOrderItem;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{

    /**
     * get model
     *
     * @return string
     */
    public function getModel(): string
    {
        return Category::class;
    }

    /**
     * Get categories with limit books
     *
     * @return Collection[Category]
     */
    public function getCategoriesWithLimitBooks()
    {
        $limitBook = (int) request()->input('limit_book', config('const.paginate.limitBooksInCategories'));
        $bookType = request()->input('book_type');
        $parentId = request()->input('parent_id');

        $query = $this->_model->with([
            'books' => function ($bookQuery) use ($bookType) {
                $bookQuery->where('books.is_limit_public', false)
                    ->where('books.public_date', '<', now());
                if ($bookType) {
                    $bookQuery->where('books.book_type', $bookType);
                }
                $bookQuery->orderBy('public_date', 'desc');
            },
        ]);
        if ($parentId) {
            $query->where('parent_id', $parentId);
        }

        return $query->get()->map(function ($category) use ($limitBook) {
            $category->books = $category->books->slice(0, $limitBook);

            return $category;
        });
    }

    /**
     * Get categories
     *
     * @return Collection
     */
    public function getMasterCategories(): Collection
    {
        $bookType = request()->input('book_type');

        $query = $this->_model
            ->whereNull('parent_id')
            ->with(['childCategories' => function ($query) use ($bookType) {
                $query->with(['childCategories' => function ($queryLever3) use ($bookType) {
                    if ($bookType) {
                        $queryLever3->whereJsonContains('book_type', $bookType);
                    }
                }]);

                if ($bookType) {
                    $query->whereJsonContains('book_type', $bookType);
                }
            }]);

        if ($bookType) {
            $query->whereJsonContains('book_type', $bookType);
        }

        return $query->get();
    }

    /**
     * Get categories by purchased books
     *
     * @return Collection
     */
    public function getCategoriesByPurchaseBooks()
    {
        $user = Auth::user();
        $bookIds = BookOrderItem::whereHas('bookOrder', function ($bookOrderQuery) use ($user) {
            $bookOrderQuery->where('user_id', $user->id)
                ->whereIn('status', [
                    BookOrderStatus::PAID,
                    BookOrderStatus::SUCCESS,
                ]);
        })->get()
            ->pluck('book_id')
            ->unique()
            ->values()
            ->toArray();

        return Category::whereHas('books', function ($bookQuery) use ($bookIds) {
            $bookQuery->whereIn('books.id', $bookIds);
        })->get();
    }
}
