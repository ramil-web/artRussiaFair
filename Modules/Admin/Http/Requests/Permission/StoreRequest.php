<?php

declare(strict_types=1);

namespace Admin\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function rules(): array
    {
        return [
//            'data' => ['required', 'array'],
//            'data.type' => ['required', 'string', 'in:roles'],
//            'data.attributes.name' => ['required', 'string', 'unique:roles,name', 'min:3'],
//            'data.attributes.description' => ['nullable', 'string'],
//            'data.relationships.permissions.data.*.id' => ['required', 'string', 'exists:permissions,id'],
//            'data.relationships.permissions.data.*.type' => ['required', 'string', 'in:permissions'],
        ];
    }
}
