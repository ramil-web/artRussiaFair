<?php

namespace Admin\Http\Requests\Vacancy;

use App\Rules\UpdateUniqueNameRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVacancyRequest extends FormRequest
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
            'id'          => 'required|exists:vacancies,id',
            'name'        => ['nullable','array', new UpdateUniqueNameRole($this->id)],
            'description' => 'nullable|array',
            'place'       => 'nullable|array',
            'status'      => 'nullable|boolean'
        ];
    }
}
