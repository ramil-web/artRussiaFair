<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class Password implements Rule
{
    public $login;

    public function __construct($login)
    {
        $this->login = $login;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $us = User::where('email', '=', $this->login)->orWhere('username', '=', $this->login)->first();

        if ($us !== null AND Hash::check($value,$us->password)) {
            return true;
        }
        return false;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Неправильный пароль';
    }
}
