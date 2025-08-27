<?php

namespace Admin\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessagesByLimitRequest extends FormRequest
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
            'chat_room_id' => 'nullable|integer|exists:chat_rooms,id',
        ];
    }
}
