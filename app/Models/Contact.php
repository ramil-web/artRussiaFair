<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $my_document_id
 * @property string $phone
 * @property string $email
 * @property string $edo_operator
 * @property string $edo_id
 */
class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'my_document_id',
        'phone',
        'email',
        'edo_operator',
        'edo_id'
    ];
    protected $hidden =['created_at', 'updated_at'];
}
