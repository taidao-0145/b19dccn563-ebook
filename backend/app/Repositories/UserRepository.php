<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * get model
     *
     * @return string
     */
    public function getModel(): string
    {
        return User::class;
    }
}
