<?php

namespace Lk\Http\Requests\InformationForPlacement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lk\Services\InformationForPlacementService;

class InformationForPlacementUpdateRequest extends FormRequest
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
            'id'                  => 'required|exists:information_for_placements,id',
            'user_application_id' => 'nullable|exists:user_applications,id',
            'name'                => 'nullable|array',
            'description'         => 'nullable|array',
            'url'                 => 'nullable|array',
            'photo'               => 'nullable|string',
            'social_network'      => 'nullable|array',
            'type'                => ['nullable', Rule::in(InformationForPlacementService::INFORMATION_TYPES)]
        ];
    }
}
