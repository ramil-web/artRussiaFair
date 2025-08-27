<?php

namespace Admin\Http\Requests\UserApplication;

use Illuminate\Foundation\Http\FormRequest;

class CommentUserApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'locate'=>'required|string',
            'message' => ['required']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
