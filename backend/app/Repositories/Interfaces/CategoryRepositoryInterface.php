<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function getCategoriesWithLimitBooks();
    public function getCategoriesByPurchaseBooks();
    public function getMasterCategories();
}
