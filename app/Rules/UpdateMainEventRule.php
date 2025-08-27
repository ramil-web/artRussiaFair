<?php

namespace App\Rules;

use App\Models\Event;
use Illuminate\Contracts\Validation\Rule;

class UpdateMainEventRule implements Rule
{
    private string $message = "Для выбранного года уже есть главное событие. Если хотите сделать главным это событие, сначала измените тип главного на другой.";

    public function __construct(public int $id, public mixed $year, public string $category)
    {
    }

    public function passes($attribute, $value): bool
    {
        $event = Event::query()->findOrFail($this->id);
        $year = $this->year ?? $event->year;
        $pattern = 'main' . $year;
        $yearMatch = $pattern == $value;
        $existedType = Event::query()->where([
            'event_type' => $value,
            'category'   => $this->category
        ])->exists();
        $isMain = preg_match('/main\d{4}/', $value);
        if (!$yearMatch && !$existedType && $isMain) {
            $this->message = "Год и тип событие не совподают, например для 2024 года тип главого событие должен выглядит примерно так: main2024";
            return false;
        }
        if ($event->event_type == $value || !$isMain) {
            return true;
        } else {
            return $yearMatch && !$existedType;
        }
    }

    public function message(): string
    {
        return $this->message;
    }
}
