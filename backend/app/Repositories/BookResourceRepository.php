<?php

namespace App\Repositories;

use App\Models\BookResource;
use App\Repositories\Interfaces\BookResourceRepositoryInterface;

class BookResourceRepository extends BaseRepository implements BookResourceRepositoryInterface
{
    /**
     * get model
     *
     * @return string
     */
    public function getModel(): string
    {
        return BookResource::class;
    }
}
