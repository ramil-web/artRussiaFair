<?php

namespace Lk\Http\Requests\Visualization;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UserApplicationAccessRule;

class VisualizationStoreRequest extends FormRequest
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
            'user_application_id' => ['required', new UserApplicationAccessRule($this->user_application_id)],
            'url'                 => 'required|array',
        ];
    }
}
