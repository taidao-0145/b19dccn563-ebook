<?php

use App\Http\Controllers\Api\BookAdController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BookCartController;
use App\Http\Controllers\Api\DeferredFeeController;
use App\Http\Controllers\Api\SlideController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BookOrderController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ShippingFeeController;
use App\Http\Controllers\Webhook\PaymentWebhookController;

// Authentication
Route::group([
    'as' => 'passport.',
    'prefix' => 'oauth',
], function () {
    Route::post('token', [
        'uses' => '\App\Http\Controllers\Api\OAuth\AccessTokenController@issueToken',
        'as' => 'token',
        'middleware' => 'throttle',
    ]);
});
Route::post('user/register', [AuthController::class, 'register']);
Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::group([
    'middleware' => ['auth:api', 'verified'],
], function () {

// Books
Route::get('books', [BookController::class, 'index']);
Route::get('books/{book}/related', [BookController::class, 'getRelatedBook']);
Route::get('books/{book}', [BookController::class, 'getDetailBook']);
Route::get('categories', [CategoryController::class, 'getCategories']);
Route::get('categories/{category}', [CategoryController::class, 'show']);
Route::get('categories-books', [CategoryController::class, 'getCategoriesWithBooks']);
Route::get('book-ads', [BookAdController::class, 'getBookAds']);
Route::get('slides', [SlideController::class, 'getSlides']);
Route::get('shipping-fees', [ShippingFeeController::class, 'getShippingFees']);
Route::get('bank-accounts', [BankAccountController::class, 'getBankAccounts']);
Route::get('formula-discounts', [DiscountController::class, 'getDiscounts']);
Route::get('deferred-fees', [DeferredFeeController::class, 'getDeferredFees']);
