<?php

namespace Lk\Classic\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ClassicUserApplicationAccessRule implements Rule
{
    protected int $userClassicApplicationId;

    public function __construct($userClassicApplicationId)
    {
        $this->userClassicApplicationId = $userClassicApplicationId;
    }

    public function passes($attribute, $value): bool
    {
        return Auth::check() && Auth::user()->userClassicApplications()->where('id', $this->userClassicApplicationId)->exists();
    }

    public function message(): string
    {
        return 'Текущий пользователь не имеет доступа к указанной заявке.';
    }
}
