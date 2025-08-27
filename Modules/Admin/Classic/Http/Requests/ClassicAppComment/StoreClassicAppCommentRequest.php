<?php

namespace Admin\Classic\Http\Requests\ClassicAppComment;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassicAppCommentRequest extends FormRequest
{

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'locate'  => 'required|string',
            'message' => ['required']
        ];
    }
}
