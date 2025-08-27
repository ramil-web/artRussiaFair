<?php

namespace Broadcast\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastLoginRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'barcode' => 'required|string|min:5|max:50'
        ];
    }
}
