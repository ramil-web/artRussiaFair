<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $place
 * @property boolean $status
 */
class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'place'
    ];

    protected $casts = [
        'name'        => Json::class,
        'description' => Json::class,
        'place'       => Json::class,
        'status'      => 'boolean',
        'created_at'  => 'datetime:Y-m-d H:m',
        'updated_at'  => 'datetime:Y-m-d H:m',
    ];
}
