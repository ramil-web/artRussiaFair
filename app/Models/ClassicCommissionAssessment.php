<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassicCommissionAssessment extends Model
{
    use HasFactory;
    use SoftDeletes;

    const MODEL_NAME = 'Оценка комиссии',
        MODEL_TYPE = 'classic-commission-assessments';

    protected $fillable = [
        'classic_user_application_id',
        'user_id',
        'status',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m',
        'deleted_at' => 'datetime:Y-m-d H:m'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classicUserApp(): BelongsTo
    {
        return $this->belongsTo(ClassicUserApplication::class);
    }
}
