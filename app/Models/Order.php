<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Znck\Eloquent\Traits\BelongsToThrough;


/**
 * @property integer $id
 * @property string $status
 * @property string $stand_area
 * @property integer $user_application_id
 * @property integer $time_slot_start_id
 * @property integer $time_slot_end_id
 * @property integer $order_item_id
 * @property object $products
 * @property object $service_catalogs
 */
class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    use BelongsToThrough;

    const MODEL_NAME = 'Заказы',
        MODEL_TYPE = 'orders';

    public $table = 'orders';

    protected $fillable = [
        'id',
        'status',
        'user_application_id',
        'time_slot_start_id',
        'time_slot_end_id',
        'stand_area',
        'order_item_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m',
        'deleted_at' => 'datetime:Y-m-d H:m',
    ];

    protected $hidden = ['deleted_at'];

    /**
     * @return BelongsTo
     */
    public function user_applications(): BelongsTo
    {
        return $this->belongsTo(UserApplication::class, 'user_application_id', 'id');
    }


    /**
     * @return BelongsTo
     */
    public function time_slot_start(): BelongsTo
    {
        return $this->belongsTo(TimeSlotStart::class, 'time_slot_start_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function time_slot_end(): BelongsTo
    {
        return $this->belongsTo(TimeSlotStart::class,  'time_slot_end_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');

    }

    /**
     * @return \Znck\Eloquent\Relations\BelongsToThrough
     */
    public function users()
    {
        return $this->belongsToThrough(User::class, UserApplication::class);
    }

}
