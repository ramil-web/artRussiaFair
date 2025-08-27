<?php

namespace Admin\Http\Requests\UserApplication;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
//            'type' => 'string|in:gallery,artist',
//            'name_gallery' => 'string|min:3|max:255',
//            'representative_name' => 'string|min:3|max:255',
//            'representative_surname' => 'string|min:3|max:255',
//            'representative_email' => 'email:rfc,dns',
//            'representative_phone' => 'string|min:3|max:255',
//            'representative_city' => 'string|min:3|max:255',
//            'about_style' => 'string|min:3|max:255',
//            'about_description' => 'string|min:3|max:255',
//            'other_fair' => 'array',
//            'social_links' => 'array',
//            'image' => 'array',
            'event_id' => 'nullable|exists:events,id',
            'status'   => 'required|string|in:new,pre_assessment,waiting,under_consideration,waiting_after_edit,confirmed,rejected,processing,approved',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
