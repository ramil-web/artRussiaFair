<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class New_passwordRule implements Rule
{
    public function __construct($new_password)
    {
        $this->new_password = $new_password;
    }

    public function passes($attribute, $value): bool
    {
        if (Hash::check($value, \Auth::user()->password)) {
            return false;
        }
        return true;
    }

    public function message(): string
    {
        return 'Новый пароль не должен совпадать со старым.';
    }
}
