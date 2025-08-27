<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryProduct extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'category_products';

    const MODEL_NAME = 'Категория товара',
        MODEL_TYPE = 'category-products';

    protected $fillable = [
        'name',
        'sort_id',
    ];


    protected $casts = [
        'name' => Json::class,
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
