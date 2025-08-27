<?php

namespace Admin\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class ChatParticipantsRequest extends FormRequest
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
            'user_id' => ['array'],
            'email' => ['string'],
            'sort_by' => 'nullable|string',
            'order_by' => 'nullable|string',
        ];
    }
}
