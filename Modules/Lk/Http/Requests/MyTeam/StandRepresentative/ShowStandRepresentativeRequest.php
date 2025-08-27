<?php

namespace Lk\Http\Requests\MyTeam\StandRepresentative;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UserAccessRule;

class ShowStandRepresentativeRequest extends FormRequest
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
            'id' => ['required', new UserAccessRule($this->id, 'StandRepresentative')],
        ];
    }
}
