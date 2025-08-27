<?php

namespace Admin\Http\Requests\Vacancy;

use App\Rules\CreateUniqueNameRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateVacancyRequest extends FormRequest
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
            'name'        => ['nullable', 'array', new CreateUniqueNameRule()],
            'description' => 'required|array',
            'place'       => 'nullable|array',
            'status'      => 'nullable|boolean'
        ];
    }
}
