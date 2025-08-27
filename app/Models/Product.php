<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'products';

    const MODEL_NAME = 'Товар',
        MODEL_TYPE = 'products';

    protected $fillable = [
        'name',
        'sort_id',
        'description',
        'specifications',
        'price',
        'category_product_id',
        'article',
        'image_path'
    ];

    public array $translatable = ['name', 'description', 'specifications'];

    protected $casts = [
        'name' => Json::class,
        'description' => Json::class,
        'specifications' => Json::class,
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m'
    ];

    public function categoryProduct(): BelongsTo
    {
        return $this->belongsTo(CategoryProduct::class);
    }

    /**
     * @return HasMany
     */
    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
