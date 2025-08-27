<?php

namespace App\Rules;


use App\Models\Vacancy;
use Illuminate\Contracts\Validation\Rule;

class UpdateUniqueNameRole implements Rule
{
    public function __construct(public int $id)
    {
    }

    public function passes($attribute, $value): bool
    {
        return !Vacancy::query()
            ->where(function ($query) use ($value) {
                $query->whereRaw("name->>'ru' = ?", [$value['ru']])
                    ->orWhereRaw("name->>'en' = ?", [$value['en']]);
            })
            ->where('id', '!=', $this->id)
            ->exists();
    }

    public function message(): string
    {
        return 'Вакансия с похожим названием уже существует, выберите другую.';
    }
}
