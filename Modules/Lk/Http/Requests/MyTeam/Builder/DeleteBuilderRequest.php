<?php

namespace Lk\Http\Requests\MyTeam\Builder;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UserAccessRule;

class DeleteBuilderRequest extends FormRequest
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
            'id' => ['required', new UserAccessRule($this->id, 'Builder')],
        ];
    }
}
