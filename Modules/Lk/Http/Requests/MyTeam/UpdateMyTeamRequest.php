<?php

namespace Lk\Http\Requests\MyTeam;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UpdateSlotInRule;
use Lk\Rules\UserAccessRule;

class UpdateMyTeamRequest extends FormRequest
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
            'id'       => ['required', new UserAccessRule($this->id, 'MyTeam')],
            'square'   => 'nullable|integer',
            'check_in' => ['nullable', new UpdateSlotInRule($this->check_in, $this->id)],
            'exit'     => ['nullable', new UpdateSlotInRule($this->exit, $this->id)],
        ];
    }
}
