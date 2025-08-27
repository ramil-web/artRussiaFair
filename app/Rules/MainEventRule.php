<?php

namespace App\Rules;

use App\Models\Event;
use Illuminate\Contracts\Validation\Rule;

class MainEventRule implements Rule
{

    public function __construct(public mixed $year, public string $category)
    {
    }

    public function passes($attribute, $value): bool
    {
        $pattern = 'main' . $this->year;
        return !($pattern == $value) || !Event::query()->where([
                'event_type' => $value,
                'category'   => $this->category
            ])->exists();
    }

    public function message(): string
    {
        return "Для выбранного года уже есть уникальное событие. Если хотите сделать главным это событие, сначала измените тип главного на другой.";
    }
}
