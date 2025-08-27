<?php

namespace Admin\Http\Requests\VipRequest;

use Illuminate\Foundation\Http\FormRequest;

class ListVipGuestRequest extends FormRequest
{

    /** Determine if the user is authorized to make this request.
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
            'filter.id' => 'nullable|integer',
            'filter.user_application_id' => 'nullable|integer',
            'filter.user_id' => 'nullable|integer',
            'sort' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
        ];
    }
}
