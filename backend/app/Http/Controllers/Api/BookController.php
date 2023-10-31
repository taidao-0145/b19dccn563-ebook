<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateBookmarkRequest;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\BookChapterResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\PurchasedBookResource;
use App\Models\Book;
use App\Services\BookmarkService;
use App\Services\BookService;
use Exception;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService,
        protected BookmarkService $bookmarkService
    ) {
    }

    /**
     * Get books by filter
     *
     * @return JsonResponse
     */
    public function index()
    {
        $books = $this->bookService->getBooksByFilter();

        return $this->responseApi(
            (new BaseResourceCollection($books))->data(BookResource::collection($books))
        );
    }

    /**
     * Get purchased books
     *
     * @return JsonResponse
     */
    public function getPurchasedBooks()
    {
        $books = $this->bookService->getPurchasedBooks();

        return $this->responseApi(
            (new BaseResourceCollection($books))->data(PurchasedBookResource::collection($books))
        );
    }

    /**
     * Get book resource
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function getBookResource(Book $book)
    {
        return $this->responseApi([
            'book_resources' => $this->bookService->getBookResources($book),
        ]);
    }

    /**
     * Get related book
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function getRelatedBook(Book $book)
    {
        return $this->responseApi([
            'books' => BookResource::collection($this->bookService->getRelatedBook($book)),
        ]);
    }

    /**
     * Get detail book
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function getDetailBook(Book $book): JsonResponse
    {
        return $this->responseApi([
            'book' => new BookResource($book),
        ]);
    }

    /**
     * Get bookmarks of book
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function getBookmarks(Book $book)
    {
        $bookmarks = $this->bookmarkService->getBookmarks($book);

        return $this->responseApi([
            'bookmarks' => $bookmarks,
        ]);
    }

    /**
     * Update bookmarks of book
     *
     * @param UpdateBookmarkRequest $request
     * @param Book $book
     * @return JsonResponse
     * @throws Exception
     */
    public function updateBookmarks(UpdateBookmarkRequest $request, Book $book)
    {
        $bookmarks = $this->bookmarkService->updateBookmarks($request, $book);

        return $this->responseApi([
            'bookmarks' => $bookmarks,
        ]);
    }

    /**
     * Get chapters of book
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function getChapters(Book $book)
    {
        $chapters = $this->bookService->getChaptersOfBook($book);

        return $this->responseApi([
            'book_chapters' => BookChapterResource::collection($chapters),
        ]);
    }
}
