<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class TimeSlotStart
 * @package App\Models
 * @property string $date
 * @property string $interval_times
 * @property integer $count
 * @property boolean $status
 * @property string $action
 * @property int $event_id
 */
class TimeSlotStart extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $table = 'time_slot_start';

    protected $fillable = ['date', 'interval_times', 'count', 'status', 'action', 'event_id'];


    public function user_applications(): HasMany
    {
        return $this->hasMany(UserApplication::class);
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

}
