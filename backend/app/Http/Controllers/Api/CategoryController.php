<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoriesWithBooksRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\MasterCategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {
    }

    /**
     *  Get categories
     *
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        $categories = $this->categoryService->getCategories();

        return $this->responseApi(
            ['categories' => MasterCategoryResource::collection($categories)]
        );
    }

    /**
     *  Get categories with books
     *
     * @param CategoriesWithBooksRequest $request
     * @return JsonResponse
     */
    public function getCategoriesWithBooks(CategoriesWithBooksRequest $request): JsonResponse
    {
        $categories = $this->categoryService->getCategoriesWithLimitBooks();

        return $this->responseApi([
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    /**
     *  Get detail a category
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        return $this->responseApi([
            'category' => new CategoryResource($category),
        ]);
    }

    /**
     *  Get categories by purchased books
     *
     * @return JsonResponse
     */
    public function getCategoriesByPurchaseBooks()
    {
        $categories = $this->categoryService->getCategoriesByPurchaseBooks();

        return $this->responseApi(
            [
                'categories' => CategoryResource::collection($categories),
            ]
        );
    }
}
