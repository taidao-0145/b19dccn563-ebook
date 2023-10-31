<?php

namespace App\Services;

use App\Enums\BookOrderStatus;
use App\Enums\BookResourceTypeEnum;
use App\Enums\BookTypeEnum;
use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface as BookRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookService
{
    public function __construct(
        protected BookRepository $bookRepository
    ) {
    }

    /**
     * Get books by filter
     *
     * @return Book
     */
    public function getBooksByFilter()
    {
        return $this->bookRepository->getBooksByFilter();
    }

    /**
     * Get purchased books
     *
     * @return Book
     */
    public function getPurchasedBooks()
    {
        $user = Auth::user();

        return $this->bookRepository->getPurchasedBooks($user);
    }

    /**
     * Get book resource
     *
     * @param Book $book
     * @return array
     */
    public function getBookResources(Book $book)
    {
        $disk = Storage::disk('s3');
        $book->load('bookResources');

        return $book->bookResources->filter(function ($bookResource) {
            return (bool) $bookResource->path;
        })->values()->map(function ($bookResource) use ($disk) {
            return [
                'path' => $disk->temporaryUrl($bookResource->path, Carbon::now()->addMinute()),
                'type' => $this->getBookResourceType($bookResource->path),
                'name' => $bookResource->name,
                'duration' => $bookResource->duration,
                'thumbnail' => $bookResource->thumbnail,
            ];
        })->toArray();
    }

    /**
     * Get related book
     *
     * @param Book $book
     * @return Book
     */
    public function getRelatedBook(Book $book)
    {
        return $this->bookRepository->getRelatedBook($book);
    }

    /**
     * Get chapters of book
     *
     * @param Book $book
     * @return Collection
     */
    public function getChaptersOfBook(Book $book): Collection
    {
        return $this->bookRepository->getChaptersOfBook($book);
    }

    /**
     * Check book was paid
     *
     * @param Book $book
     * @return bool
     */
    public function isBookWasPaid(Book $book): bool
    {
        $user = Auth::user();
        $book->load([
            'bookOrders' => function ($bookOrdersQuery) use ($user) {
                $bookOrdersQuery->where('user_id', $user->id)
                    ->whereIn('status', [
                        BookOrderStatus::PAID,
                        BookOrderStatus::SUCCESS,
                    ]);
            },
        ]);

        return (bool) $book->bookOrders->count();
    }

    /**
     * Get digital book in request
     *
     * @param Request $request
     * @return Book|null
     */
    public function getDigitalBookInRequest(Request $request): Book|null
    {
        $bookIds = collect($request->books)
            ->pluck('id')
            ->toArray();
        $books = $this->bookRepository->whereIn('id', $bookIds)->get();

        return $books->first(function ($book) {
            return $book->book_type != BookTypeEnum::BOOK_TYPE;
        });
    }

    /**
     * Get book resource type
     *
     * @param string $path
     * @return string
     */
    protected function getBookResourceType($path)
    {
        if (preg_match('/\.pdf$/', $path)) {
            return BookResourceTypeEnum::PDF_TYPE;
        }
        if (preg_match('/\.mp4|\.m3u8$/', $path)) {
            return BookResourceTypeEnum::VIDEO_TYPE;
        }

        return '';
    }
}
