<?php

namespace Admin\Http\Requests\Vacancy;

use Admin\Http\Requests\Auth\ForgotPasswordRequest;

class ShowVacancyRequest extends ForgotPasswordRequest
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
         'id' => 'required|exists:vacancies,id'
        ];
    }
}
