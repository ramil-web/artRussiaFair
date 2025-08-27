<?php

namespace Lk\Http\Requests\MyDocuments;

use Illuminate\Foundation\Http\FormRequest;

class MyDocumentStoreRequest extends FormRequest
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
            'user_application_id'   => 'required|exists:user_applications,id',
            'status'                => 'required|string|in:individual,legal_entity,self-employed,sole_entrepreneur',
            'payment_account'       => 'required_if:status,self-employed,individual|nullable|string|min:18|max:24',
            'bank_name'             => 'required_if:status,self-employed,individual|nullable|string',
            'bic'                   => 'required_if:status,self-employed,individual|nullable|string|min:8|max:14',
            'correspondent_account' => 'required_if:status,self-employed,individual|nullable|string|min:18|max:24',
            'kpp'                   => 'required_if:status,self-employed,individual|nullable|string|min:8|max:14',
            'inn'                   => 'required_if:status,self-employed,individual|nullable|string|min:8|max:14',
            'phone'                 => 'required|string|max:20',
            'email'                 => 'required|email:rfc,dns',
            'edo_operator'          => 'nullable|string|min:3|max:100',
            'edo_id'                => 'nullable|string|min:3|max:100',
            'files'                 => 'required|array',
        ];
    }
}
