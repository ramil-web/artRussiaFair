<?php

namespace App\Repositories\Participant;

use App\Exceptions\CustomException;
use App\Models\Participant;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ParticipantRepository extends BaseRepository
{
    public function __construct(Participant $model)
    {
        parent::__construct($model);
    }


    /**
     * @param mixed $slug
     * @param array $allowedFields
     * @return Model|QueryBuilder
     * @throws CustomException
     */
    public function findBySlug(mixed $slug, array $allowedFields): Model|QueryBuilder
    {
        try {
            $query = QueryBuilder::for($this->model);
            $query = $query->allowedFields($allowedFields);
            return $query->where('slug', $slug)->firstOrFail();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
