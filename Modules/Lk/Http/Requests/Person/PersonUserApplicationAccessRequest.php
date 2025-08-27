<?php

namespace Lk\Http\Requests\Person;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UserApplicationAccessRule;

class PersonUserApplicationAccessRequest extends FormRequest
{

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'id'                  => 'nullable|id',
            'user_application_id' => ['nullable', new UserApplicationAccessRule($this->user_application_id)],
        ];
    }

}
