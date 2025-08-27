<?php

namespace Lk\Http\Requests\Hardware;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHardwareRequest extends FormRequest
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
            'quantity'   => 'nullable|integer',
            'order_id'   => 'nullable|int|exists:orders,id',
            'product_id' => 'nullable|int|exists:products,id',
        ];
    }
}
