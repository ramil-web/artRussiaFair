<?php

namespace Lk\Http\Requests\VipGuests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteVipGuestRequest extends FormRequest
{
    /**
     * @return true
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
            'id' => 'required|exists:vip_guests,id',
        ];
    }
}
