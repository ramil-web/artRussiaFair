<?php

declare(strict_types=1);

namespace Admin\Http\Requests\Permission;


use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    protected function rules(): array
    {
        $roleId = $this->route('id');

        return [
//            'data' => ['required', 'array'],
//            'data.type' => ['required', 'string', 'in:roles'],
//            'data.attributes.name' => ['string', 'min:5', Rule::unique('roles', 'name')->ignore($roleId)],
//            'data.attributes.description' => ['nullable', 'string'],
//            'data.relationships.permissions.data.*.id' => ['string', 'exists:permissions,id'],
//            'data.relationships.permissions.data.*.type' => ['string', 'in:permissions'],
        ];
    }
}
