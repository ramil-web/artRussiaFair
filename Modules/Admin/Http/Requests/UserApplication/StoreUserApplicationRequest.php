<?php

namespace Admin\Http\Requests\UserApplication;

use Illuminate\Foundation\Http\FormRequest;

//use Illuminate\Http\Request;
class StoreUserApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required|string|in:gallery,artist',
            'name_gallery' => 'required|string|min:3|max:255',
            'representative_name' => 'required|string|min:3|max:255',
            'representative_surname' => 'required|string|min:3|max:255',
            'representative_email' => 'required|email:rfc,dns',
            'representative_phone' => 'required|string|min:3|max:255',
            'representative_city' => 'required|string|min:3|max:255',
            'about_style' => 'required|string|min:3|max:255',
            'about_description' => 'required|string|min:3|max:255',
            'other_fair' => 'array',
            'social_links' => 'array',
            'image' => 'array'
//            'status'=>'required|string|in:новая,на рассмотрении,доработка,подтверждена,отклонена'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
