<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'isbn',
        'c_code',
        'hash_id',
        'name',
        'description',
        'thumbnail',
        'author_name',
        'page_number',
        'cover_page',
        'public_date',
        'is_limit_public',
        'inventory_number',
        'price',
        'sample_path',
        'book_type',
    ];

    public function getRouteKeyName()
    {
        return 'hash_id';
    }

    public function bookResources(): HasMany
    {
        return $this->hasMany(BookResource::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, BookCategory::class, 'book_id', 'category_id');
    }

    public function bookOrders(): BelongsToMany
    {
        return $this->belongsToMany(BookOrder::class, BookOrderItem::class, 'book_id', 'book_order_id');
    }

    public function usersAddToCart(): BelongsToMany
    {
        return $this->belongsToMany(User::class, BookCart::class, 'book_id', 'user_id');
    }
}
