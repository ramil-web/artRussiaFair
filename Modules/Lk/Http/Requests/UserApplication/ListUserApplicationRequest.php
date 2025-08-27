<?php

namespace Lk\Http\Requests\UserApplication;

use Illuminate\Foundation\Http\FormRequest;

class ListUserApplicationRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter.id'       => 'nullable|exists:user_applications,id',
            'filter.status'   => 'nullable|string',
            'filter.category' => 'required|string',
        ];
    }
}
