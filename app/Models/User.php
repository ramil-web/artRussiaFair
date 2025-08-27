<?php

namespace App\Models;

use App\Jobs\Chat\SendMessageToMailJob;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use LaravelAndVueJS\Traits\LaravelPermissionToVueJS;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;


/**
* @property string $userApplications
* @property string $email
* @property string $userClassicApplications
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use HasRoles;
    use LaravelPermissionToVueJS;
    use Notifiable;
    use SoftDeletes;


    const  MODEL_TYPE = 'users';

    public $table = 'users';

    protected $fillable = ['name', 'username', 'email', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
        'deleted_at' => 'datetime:Y-m-d',
    ];

    /**
     * @return HasOne
     */
    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * @return HasOne
     */
    public function managerProfile(): HasOne
    {
        return $this->hasOne(ManagerProfile::class);
    }

    /**
     * @return HasMany
     */
    public function comment(): HasMany
    {
        return $this->hasMany(AppComment::class);
    }

    /**
     * @return HasMany
     */
    public function assessment(): HasMany
    {
        return $this->hasMany(CommissionAssessment::class);
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $query = http_build_query([
            'token' => $token,
            'email' => $this->email,
            'form' => 'reset'
        ]);

        $prefix  = $this->hasAnyRole([
            'manager',
            'commission'
        ]) ? 'admin' : 'lk';
        $url = sprintf("%s?%s", env("FORGOT_PASSWORD"), $query);
        SendMessageToMailJob::dispatch(
            'Восстановления пароля',
            'emails.forgot-password-mail',
            [
                'email' => $this->email,
                'url' => $url
            ]
        );
    }


    /**
     * @return Attribute
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Hash::make($value),
        );
    }


    public function userApplications(): HasMany
    {
        return $this->hasMany(UserApplication::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function userClassicApplications(): HasMany
    {
        return $this->hasMany(ClassicUserApplication::class);
    }
}
