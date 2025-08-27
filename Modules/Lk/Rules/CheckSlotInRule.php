<?php

namespace Lk\Rules;


use App\Exceptions\CustomException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class CheckSlotInRule implements Rule
{
    public function __construct(public int $slotId, public string $model)
    {
    }

    /**
     * @throws CustomException
     */
    public function passes($attribute, $value)
    {
        try {

            $model = app("App\Models\\$this->model");
            return $model->query()
                ->where('id', $this->slotId)
                ->where('count', '<', 2)
                ->exists();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function message(): string
    {
        return "К сожелению слот уже занял другой участник, выбирайте другой слот пожалуйста.";
    }
}
