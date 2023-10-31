<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hash_id',
        'code',
        'user_id',
        'total_price',
        'shipping_fee',
        'deferred_fee',
        'discount_amount',
        'discount_percent',
        'receiver_email',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'receiver_zipcode',
        'memo',
        'stripe_payment_intent_id',
        'shipping_status',
        'status',
        'payment_method',
    ];

    public function getRouteKeyName()
    {
        return 'hash_id';
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function books(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Book::class, BookOrderItem::class, 'book_order_id', 'book_id');
    }

    public function bookOrderItems(): hasMany
    {
        return $this->hasMany(BookOrderItem::class, 'book_order_id', 'id');
    }
}
