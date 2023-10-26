<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {
    }
}
