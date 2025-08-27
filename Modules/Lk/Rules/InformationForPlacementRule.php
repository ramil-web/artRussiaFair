<?php

namespace Lk\Rules;

use App\Exceptions\CustomException;
use App\Models\InformationForPlacement;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class InformationForPlacementRule implements Rule
{

    public function __construct(public int $userApplicationId)
    {
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     * @throws CustomException
     */
    public function passes($attribute, $value): bool
    {
        try {
            return !InformationForPlacement::query()
                ->where([
                    $attribute            => $value,
                    'user_application_id' => $this->userApplicationId
                ])
                ->exists();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function message(): string
    {
        return "Информация для размещения такого типа, уже существует для данной заявки";
    }
}
