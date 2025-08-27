<?php

namespace Admin\Http\Requests\Manager;

use App\Enums\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateManagerRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => ['string',
                Rule::unique('users', 'username')->ignore((int)$this->route('id')),
                'max:255'],
            'email' => ['string', 'email', Rule::unique('users', 'email')->ignore((int)$this->route('id')),'max:255'],
            'role' => [
                 'string',
                Rule::in([UserRoleEnum::MANAGER, UserRoleEnum::SUPER_ADMIN, UserRoleEnum::COMMISSION])
            ]
        ];
    }

}
