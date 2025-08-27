<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'product_id',
        'created_at',
        'updated_at',
        'codes_2025'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'codes_2025'
    ];
}
