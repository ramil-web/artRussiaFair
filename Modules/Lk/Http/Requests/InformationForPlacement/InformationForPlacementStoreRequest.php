<?php

namespace Lk\Http\Requests\InformationForPlacement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lk\Rules\InformationForPlacementRule;
use Lk\Rules\UserApplicationAccessRule;
use Lk\Services\InformationForPlacementService;

class InformationForPlacementStoreRequest extends FormRequest
{
    const TYPES = [
        'for_app',
        'for_catalog'
    ];

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
            'name'                => 'nullable|array',
            'description'         => 'nullable|array',
            'user_application_id' => ['required', new UserApplicationAccessRule($this->user_application_id)],
            'social_network'      => 'required_if:type,for_general_information|nullable|array',
            'url'                 => 'required_if:type,for_app,for_social_network|nullable|array',
            'photo'               => 'required_if:type,for_app,for_catalog,for_social_network|nullable|string',
            'type'                => ['required',  Rule::in(InformationForPlacementService::INFORMATION_TYPES), new InformationForPlacementRule($this->user_application_id)]
        ];
    }
}
