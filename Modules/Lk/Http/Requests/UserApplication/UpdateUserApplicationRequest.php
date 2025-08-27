<?php

namespace Lk\Http\Requests\UserApplication;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type'                   => 'string|in:gallery,artist',
            'name_gallery'           => 'string|nullable',
            'representative_name'    => 'string|max:255',
            'representative_surname' => 'string|max:255',
            'representative_email'   => 'email:rfc,dns',
            'representative_phone'   => 'string|min:3|max:255',
            'representative_city'    => 'string|max:255',
            'about_style'            => 'string|max:255',
            'about_description'      => 'string',
            'other_fair'             => 'array',
            'social_links'           => 'array',
            'image'                  => 'array',
            'locate'                 => 'string',
            'event_id'               => 'nullable|exists:events,id',
            'education'              => 'nullable|string'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
