<?php

declare(strict_types=1);

namespace Admin\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $commonRules = [
//            'data' => ['required', 'array'],
//            'data.type' => ['required', 'string', 'in:permissions'],
//            'data.attributes.description' => ['nullable', 'string'],
//            'data.relationships.roles.data.*.id' => ['string', 'exists:roles,id'],
//            'data.relationships.roles.data.*.type' => ['string', 'in:roles'],
        ];

        return array_merge($commonRules, $this->permissionNameRules());
    }

    private function permissionNameRules(): array
    {
        $permissionMinLength = config('admin.rules.permission.name.min');

        return [
            'data.attributes.name' => [
                'required',
                'string',
                "min:$permissionMinLength",
                $this->isMethod($this::METHOD_POST)
                    ? 'unique:permissions,name'
                    : Rule::unique('permissions', 'name')->ignore((int) $this->route('permission')),
            ]
        ];
    }
}

