<?php

namespace Lk\Http\Requests\MyTeam\StandRepresentative;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UserAccessRule;

class UpdateStandRepresentativeRequest extends FormRequest
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
            'id'        => ['required', new UserAccessRule($this->id, 'StandRepresentative')],
            'full_name' => 'nullable|string',
            'passport'  => "nullable|unique:stand_representatives,passport,$this->id",
        ];
    }
}
