<?php

namespace Lk\Http\Requests\Auth;

use App\Rules\Login;
use App\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'login'    => ['bail', 'nullable', 'string', 'min:1', 'max:255', new Login],
            'password' => ['nullable', 'max:255', new Password($this->login)],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'login.required'    => 'логин, обязательное поле',
            'login.min'         => 'Должен быть больше 4',
            'password.required' => 'Пароль обязательное поле',
        ];
    }
}
