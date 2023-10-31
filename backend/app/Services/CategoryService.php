<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoryRepositoryInterface as CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        protected CategoryRepository $categoryRepository
    ) {
    }

    /**
     * Get categories
     *
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categoryRepository->getMasterCategories();
    }

    /**
     * Get categories
     *
     * @return Collection
     */
    public function getCategoriesWithLimitBooks(): Collection
    {
        return $this->categoryRepository->getCategoriesWithLimitBooks();
    }

    /**
     * Get categories by purchased books
     *
     * @return Collection
     */
    public function getCategoriesByPurchaseBooks(): Collection
    {
        return $this->categoryRepository->getCategoriesByPurchaseBooks();
    }
}
