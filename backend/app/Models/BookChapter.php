<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'index',
        'book_resource_id',
    ];

    public function bookResource(): BelongsTo
    {
        return $this->belongsTo(BookResource::class);
    }
}
