<?php

namespace Admin\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageUpdateRequest extends FormRequest
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
            'id' => 'required|exists:chat_messages,id',
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'message' => 'nullable|string'
        ];
    }
}
