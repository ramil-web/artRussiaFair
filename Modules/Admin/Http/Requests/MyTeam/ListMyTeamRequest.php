<?php

namespace Admin\Http\Requests\MyTeam;

use Illuminate\Foundation\Http\FormRequest;

class ListMyTeamRequest extends FormRequest
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
            'sort' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
        ];
    }
}
