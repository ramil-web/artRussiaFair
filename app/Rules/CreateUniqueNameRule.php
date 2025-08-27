<?php

namespace App\Rules;

use App\Models\Vacancy;
use Illuminate\Contracts\Validation\Rule;

class CreateUniqueNameRule implements Rule
{

    public function passes($attribute, $value): bool
    {
        return !Vacancy::query()
            ->where(function ($query) use ($value) {
                $query->whereRaw("name->>'ru' = ?", [$value['ru']])
                    ->orWhereRaw("name->>'en' = ?", [$value['en']]);
            })->exists();
    }

    public function message(): string
    {
        return 'Вакансия с похожим названием уже существует, выберите другую.';
    }
}
