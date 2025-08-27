<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class Old_passwordRule implements Rule
{
    public function __construct($old_pass)
    {
        $this->old_pass = $old_pass;
    }

    public function passes($attribute, $value): bool
    {

//        dd(!Hash::check($this->old_pass, \Auth::user()->password));
        if (!Hash::check($value, \Auth::user()->password)) {
            return false;
        }
        return true;
    }

    public function message(): string
    {
        return 'Старый пароль не совпадает.';
    }
}
