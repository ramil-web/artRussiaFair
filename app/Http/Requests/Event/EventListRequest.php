<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class EventListRequest extends FormRequest
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
        $date = intval(date("Y")) + 1;
        return [
            'year'                => "nullable|digits:4|integer|min:2000|max:$date",
            'has_partners'        => 'nullable|string',
            'filter.category'     => 'nullable|string',
            'partner_category_id' => 'nullable|exists:partner_categories,id',
        ];
    }
}
