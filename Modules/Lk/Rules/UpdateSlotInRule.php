<?php

namespace Lk\Rules;

use App\Exceptions\CustomException;
use App\Models\MyTeam;
use App\Models\TimeSlotStart;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class UpdateSlotInRule implements Rule
{
    public function __construct(public ?int $slotId, public string $id)
    {
    }

    /**
     * @throws CustomException
     */
    public function passes($attribute, $value): bool
    {
        try {
            if (is_null($value)) {
                return true;
            }

            $myTeam = MyTeam::query()
                ->findOrFail($this->id);

            if ($myTeam->$attribute == $this->slotId) {
                return true;
            }

            return TimeSlotStart::query()
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
