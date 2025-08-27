<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $url
 */
class SchemaOfStand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url'
    ];
}
