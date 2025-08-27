<?php


namespace Admin\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'token' => ['required'],
            'password' => ['required', 'string'],
            'confirm_password' => 'required|same:password',
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ];
    }
}
