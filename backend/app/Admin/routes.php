<?php

use App\Admin\Controllers\BankAccountController;
use App\Admin\Controllers\BookAdController;
use App\Admin\Controllers\BookController;
use App\Admin\Controllers\BookOrderController;
use App\Admin\Controllers\BookResourceController;
use App\Admin\Controllers\CategoryController;
use App\Admin\Controllers\DeferredFeeController;
use App\Admin\Controllers\DiscountController;
use App\Admin\Controllers\SettingController;
use App\Admin\Controllers\ShippingFeeController;
use App\Admin\Controllers\SlideController;
use App\Admin\Controllers\UserController;
use Encore\Admin\Facades\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {
    $router->get('/', 'HomeController@index')->name('home');

    // books
    $router->resource('books', BookController::class);
    $router->post('books', 'BookController@createBook');

    $router->resource('categories', CategoryController::class);
    $router->resource('users', UserController::class);
    $router->resource('book-orders', BookOrderController::class)->only(['update', 'edit', 'index', 'show']);
    $router->resource('slides', SlideController::class);
    $router->resource('book-ads', BookAdController::class);
    $router->resource('settings', SettingController::class);
    $router->resource('bank-accounts', BankAccountController::class);
    $router->resource('shipping-fees', ShippingFeeController::class);
    $router->resource('book-resources', BookResourceController::class)->only(['update', 'edit', 'show']);
    $router->resource('deferred-fees', DeferredFeeController::class);
    $router->resource('discounts', DiscountController::class);
});

