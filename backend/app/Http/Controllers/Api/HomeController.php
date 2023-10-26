<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;

class HomeController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function index()
    {
        return $this->responseApi('Api');
    }
}
