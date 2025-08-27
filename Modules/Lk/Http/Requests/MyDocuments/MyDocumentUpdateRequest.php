<?php

namespace Lk\Http\Requests\MyDocuments;

use Illuminate\Foundation\Http\FormRequest;

class MyDocumentUpdateRequest extends FormRequest
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
            'id'                    => 'required|exists:my_documents,id',
            'user_application_id'   => 'required|exists:user_applications,id',
            'status'                => 'required|string|in:individual,legal_entity,self-employed,sole_entrepreneur',
            'payment_account'       => 'nullable|string|min:18|max:24',
            'bank_name'             => 'nullable|string',
            'bic'                   => 'nullable|string|min:8|max:14',
            'correspondent_account' => 'nullable|string|min:18|max:24',
            'kpp'                   => 'nullable|string|min:8|max:14',
            'inn'                   => 'nullable|string|min:8|max:14',
            'phone'                 => 'nullable|string|min:3|max:20',
            'email'                 => 'nullable|email:rfc,dns',
            'edo_operator'          => 'nullable|string|min:3|max:100',
            'edo_id'                => 'nullable|string|min:3|max:100',
            'files'                 => 'nullable|array',
        ];
    }
}
