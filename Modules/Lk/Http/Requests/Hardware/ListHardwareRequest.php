<?php

namespace Lk\Http\Requests\Hardware;

use Illuminate\Foundation\Http\FormRequest;

class ListHardwareRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'with_trashed' => 'nullable|string'
        ];
    }
}
