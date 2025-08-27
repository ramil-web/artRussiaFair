<?php

namespace Lk\Http\Requests\UserApplication;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type'                   => 'required|string|in:gallery,artist',
            'name_gallery'           => 'string|nullable',
            'representative_name'    => 'required|string|max:255',
            'representative_surname' => 'required|string|max:255',
            'representative_email'   => 'required|email:rfc,dns',
            'representative_phone'   => 'required|string|min:3|max:255',
            'representative_city'    => 'required|string|max:255',
            'about_style'            => 'required|string|max:255',
            'about_description'      => 'string',
            'other_fair'             => 'array',
            'social_links'           => 'array',
            'image'                  => 'array',
            'locate'                 => 'string',
            'time_slot_start_id'     => 'nullable|numeric|gt:0',
            'event_id'               => 'required|exists:events,id',
            'education'              => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
