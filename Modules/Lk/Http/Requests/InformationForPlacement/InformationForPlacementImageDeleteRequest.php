<?php

namespace Lk\Http\Requests\InformationForPlacement;

use Illuminate\Foundation\Http\FormRequest;

class InformationForPlacementImageDeleteRequest extends FormRequest
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
            'id'    => 'required|exists:information_for_placements,id',
            'image' => 'required|string'
        ];
    }
}
