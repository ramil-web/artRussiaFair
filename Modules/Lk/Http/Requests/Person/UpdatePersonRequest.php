<?php

namespace Lk\Http\Requests\Person;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UserApplicationAccessRule;

class UpdatePersonRequest extends FormRequest
{
    /**
     * @return true
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_application_id' => ['nullable', new UserApplicationAccessRule($this->user_application_id)],
            'full_name'           => 'nullable|string',
            'passport'            => 'nullable|string',
        ];
    }
}
