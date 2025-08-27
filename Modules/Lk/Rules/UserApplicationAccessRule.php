<?php

namespace Lk\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserApplicationAccessRule implements Rule
{
    protected $userApplicationId;

    public function __construct($userApplicationId)
    {
        $this->userApplicationId = $userApplicationId;
    }

    public function passes($attribute, $value)
    {
        return Auth::check() && Auth::user()->userApplications()->where('id', $this->userApplicationId)->exists();
    }

    public function message()
    {
        return 'Текущий пользователь не имеет доступа к указанной заявке.';
    }
}
