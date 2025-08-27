<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ServiceCatalog extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasTranslations;

    protected $table = 'service_catalogs';

    const MODEL_NAME = 'Каталог услуг',
        MODEL_TYPE = 'service_catalogs';

    protected $fillable = [
        'name',
        'sort_id',
        'image',
        'description',
        'category',
        'other',
        'price',
    ];

    public array $translatable = ['name', 'category', 'description', 'other'];

    protected $casts = [
        'name' => Json::class,
        'description' => Json::class,
        'category' => Json::class,
        'other' => Json::class,
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m',
    ];

    /**
     * @return HasMany
     */
    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
