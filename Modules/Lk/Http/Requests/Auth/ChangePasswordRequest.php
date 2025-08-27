<?php

namespace Lk\Http\Requests\Auth;

use App\Rules\New_passwordRule;
use App\Rules\Old_passwordRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'old_password'     => ['required', new Old_passwordRule($this->old_password)],
            'new_password'     => ['required', 'min:6', new New_passwordRule($this->new_password)],
            'confirm_password' => ['required', 'same:new_password'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
