<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $my_document_id
 * @property string $payment_account
 * @property string $bank_name
 * @property string $bic
 * @property string $correspondent_account
 * @property string $kpp
 * @property string $inn
 */
class Requisite extends Model
{
    use HasFactory;

    protected $fillable = [
        'my_document_id',
        'payment_account',
        'bank_name',
        'bic',
        'correspondent_account',
        'kpp',
        'inn'
    ];
    protected $hidden =['created_at', 'updated_at'];
}
