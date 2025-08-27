<?php

namespace Lk\Http\Requests\UserApplication;

use Illuminate\Foundation\Http\FormRequest;

class StatusUserApplicationRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'required|string|exists:events,category',
        ];
    }
}
