<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
* @property  integer $id
* @property  integer $event_id
* @property  string $name
* @property  string $path
 */

class AdminDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'event_id',
        'name',
    ];
}
