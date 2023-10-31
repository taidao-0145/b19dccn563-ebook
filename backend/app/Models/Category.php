<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = [
        'name',
        'description',
        'book_type',
    ];

    protected $casts = [
        'book_type' => 'array',
    ];

    public function books(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Book::class, BookCategory::class, 'category_id', 'book_id');
    }
    public function childCategories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            if ($category->books()->exists()) {
                throw new \Exception(PHP_EOL . __('admin.exception.category_foreign_key'));
            }
            if ($category->childCategories()->exists()) {
                throw new \Exception(PHP_EOL . __('admin.exception.category_foreign_key'));
            }
        });
    }
}
