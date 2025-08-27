<?php

namespace Admin\Classic\Http\Requests\ClassicUserApplication;

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
     * @return array
     */
    public function rules(): array
    {
        return [
            'id'       => 'required|exists:classic_user_applications,id',
            'event_id' => 'nullable|exists:classic_events,id',
            'status'   => 'required|string|in:new,pre_assessment,waiting,under_consideration,waiting_after_edit,confirmed,rejected,processing,approved',
        ];
    }
}
