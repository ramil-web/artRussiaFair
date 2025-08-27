<?php

namespace Admin\Services;

use Admin\Repositories\Person\PersonRepository;
use App\Enums\PersonTypesEnum;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PersonService
{

    public function __construct(public PersonRepository $personRepository)
    {
    }

    /**
     * @param array $dataApp
     * @param PersonTypesEnum $type
     * @return Collection|QueryBuilder[]
     * @throws CustomException
     */
    public function list(array $dataApp, PersonTypesEnum $type): Collection|QueryBuilder
    {

        $where = ['type' => $type];
        $withRelation = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('user_application_id'),
        ];
        $allowedFields = ['id', 'user_application_id', 'full_name', 'passport', 'type', 'created_at', 'updated_at'];
        $allowedIncludes = [];
        $allowedSorts = ['id', 'user_application_id', 'created_at', 'updated_at'];
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;

        return $this->personRepository->getAllByFilterAndType(
            $where,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            false,
            $perPage,
        );
    }

    /**
     * @param int $id
     * @param PersonTypesEnum $type
     * @return Model|null
     * @throws CustomException
     */
    public function show(int $id, PersonTypesEnum $type): Model|null
    {
        try {
            $where = ['type' => $type];
            $withRelation = ['users', 'user_applications'];
            return $this->personRepository->findByIdAndType($id, $withRelation, $where);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
