<?php

namespace Lk\Http\Requests\VipGuests;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UserApplicationAccessRule;

class UpdateVipGuestRequest extends FormRequest
{
    /**
     * @return true
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'full_name'           => 'nullable|string',
            'user_application_id' => ['nullable', new UserApplicationAccessRule($this->user_application_id)],
            'organization'        => 'nullable|string',
            'email'               => 'nullable|email',
        ];
    }
}
