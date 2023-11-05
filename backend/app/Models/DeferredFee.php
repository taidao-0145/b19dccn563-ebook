<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeferredFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'from_price',
        'to_price',
        'fee',
    ];
}
