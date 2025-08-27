<?php

namespace App\Rules\Classic;

use App\Models\ClassicEvent;
use Illuminate\Contracts\Validation\Rule;

class MainClassicEventRule implements Rule
{

    public function __construct(public mixed $year)
    {
    }

    public function passes($attribute, $value): bool
    {
        $pattern = 'main' . $this->year;
        return !($pattern == $value) || !ClassicEvent::query()->where('event_type', $value)->exists();
    }

    public function message(): string
    {
        return "Для выбранного года уже есть уникальное событие. Если хотите сделать главным это событие, сначала измените тип главного на другой.";
    }
}
