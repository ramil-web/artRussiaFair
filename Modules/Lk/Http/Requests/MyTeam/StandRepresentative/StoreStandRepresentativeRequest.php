<?php

namespace Lk\Http\Requests\MyTeam\StandRepresentative;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UserApplicationAccessRule;

class StoreStandRepresentativeRequest extends FormRequest
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
            'user_application_id' => ['required', new UserApplicationAccessRule($this->user_application_id)],
            'full_name'           => 'required|string',
            'passport'            => 'required|unique:stand_representatives,passport',
        ];
    }
}
