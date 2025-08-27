<?php

namespace Broadcast\Services;

use App\Exceptions\CustomException;
use App\Models\Broadcast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class BroadcastService
{

    /**
     * @param string $barCode
     * @return Model|Builder|null
     * @throws CustomException
     */
    public function logIn(string $barCode): Model|Builder|null
    {
        try {
            return Broadcast::query()
                ->where('barcode', $barCode)
                ->first();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
