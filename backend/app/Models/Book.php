<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'name',
        'description',
        'thumbnail',
        'author_name',
        'page_number',
        'relase_date',
        'inventory_number',
        'price',
        'sample_path',
        'book_type',
    ];
}
