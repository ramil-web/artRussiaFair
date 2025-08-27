<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Json implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param string $key
     * @param  mixed  $value
     * @param array $attributes
     * @return array
     */
    public function get($model, string $key, $value, array $attributes): array
    {

        if ($value === null || $value === '' || $value === 'null') {
            return [];
        }

        $decoded = json_decode($value, true);

        // Если это уже массив (в том числе объект) — возвращаем
        if (is_array($decoded)) {
            return $decoded;
        }

        // Если это строка с JSON — пробуем декодировать
        if (is_string($decoded)) {
            $inner = json_decode($decoded, true);
            return is_array($inner) ? $inner : [];
        }

        return [];
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param string $key
     * @param  array  $value
     * @param array $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value);
    }
}
