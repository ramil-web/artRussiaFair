<?php

namespace Lk\Classic\Http\Requests\ClassicUserApplication;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassicUserApplicationRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'id'                     => 'required|exists:classic_user_applications,id',
            'type'                   => 'nullable|string|in:gallery,artist',
            'name_gallery'           => 'string|nullable',
            'representative_name'    => 'nullable|string|max:255',
            'representative_surname' => 'nullable|string|max:255',
            'representative_email'   => 'nullable|email:rfc,dns',
            'representative_phone'   => 'nullable|string|min:3|max:255',
            'representative_city'    => 'nullable|string|max:255',
            'about_style'            => 'nullable|string|max:255',
            'about_description'      => 'string',
            'other_fair'             => 'array',
            'social_links'           => 'array',
            'image'                  => 'array',
            'locate'                 => 'string',
            'time_slot_start_id'     => 'nullable|numeric|gt:0',
            'classic_event_id'       => 'nullable|exists:classic_events,id'
        ];
    }
}
