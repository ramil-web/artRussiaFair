<?php

declare(strict_types=1);

namespace Admin\Http\Requests\Role;


use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('id');

        return [
            'desc' => ['nullable', 'string'],
            'permissions' => ['array'],
        ];
    }
}
