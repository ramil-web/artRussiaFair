<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_application_id
 * @property string $full_name
 * @property string $passport
 */
class StandRepresentative extends Model
{
    use HasFactory;

    protected $hidden =['user_application_id'];

    protected $fillable = [
        'user_application_id',
        'full_name',
        'passport'
    ];
}
