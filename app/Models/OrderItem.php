<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property  integer $id
 * @property integer $quantity
 * @property integer $order_id
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property object $order_items
 * @property object $additional_services
 * @property object $hardware
 * @property object $specifications
 * @property string $image
 * @property object $name
 * @property string $article
 * @property integer $product_id
 * @property integer $service_catalog_id
 */
class OrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    const MODEL_NAME = 'Элементы заказа',
        MODEL_TYPE = 'order-item';

    protected $fillable = [
        'id',
        'quantity',
        'type',
        'order_id',
        'service_catalog_id',
        'product_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = ['deleted_at', 'type'];

    /**
     * @return BelongsTo
     */
    public function orders(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * @return HasManyThrough
     */
    public function user_applications(): HasManyThrough
    {
        return $this->hasOneThrough(
            UserApplication::class,
            Order::class,
            'id',
            'id',
            'order_id',
            'user_application_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function service_catalogs(): BelongsTo
    {
        return $this->belongsTo(ServiceCatalog::class,  'service_catalog_id', 'id');
    }
}
