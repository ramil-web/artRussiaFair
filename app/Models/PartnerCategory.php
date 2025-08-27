<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'partner_categories';

    const MODEL_NAME = 'Категория партнера',
        MODEL_TYPE = 'partner-category';

    protected $fillable = [
        'id',
        'name',
        'sort_id',
    ];

    protected $casts = [
        'name' => Json::class
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Partner::class);
    }
}
