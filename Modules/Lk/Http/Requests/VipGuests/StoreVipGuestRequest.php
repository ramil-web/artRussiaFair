<?php

namespace Lk\Http\Requests\VipGuests;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UserApplicationAccessRule;

class StoreVipGuestRequest extends FormRequest
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
            'full_name'           => 'required|string',
            'user_application_id' => ['required', new UserApplicationAccessRule($this->user_application_id)],
            'organization'        => 'required|string',
            'email'               => 'required|email',
        ];
    }
}
