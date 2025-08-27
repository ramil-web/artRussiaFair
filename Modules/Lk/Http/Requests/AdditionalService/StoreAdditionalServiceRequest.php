<?php

namespace Lk\Http\Requests\AdditionalService;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdditionalServiceRequest extends FormRequest
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
            'quantity'           => 'required|integer',
            'order_id'           => 'required|int|exists:orders,id',
            'service_catalog_id' => 'required|int|exists:service_catalogs,id',
        ];
    }
}
