<?php

namespace Lk\Services;

use App\Enums\AppStatusEnum;
use App\Enums\PersonTypesEnum;
use App\Exceptions\CustomException;
use App\Http\Responses\ApiErrorResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\Person\PersonRepository;
use Lk\Repositories\UserApplication\UserApplicationRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PersonService
{

    public function __construct(
        public PersonRepository          $personRepository,
        public UserApplicationRepository $userApplicationRepository
    )
    {
    }

    /**
     * @param int $userId
     * @param array $dataApp
     * @param PersonTypesEnum $type
     * @return Collection|QueryBuilder[]
     * @throws CustomException
     */
    public function list(int $userId, array $dataApp, PersonTypesEnum $type): Collection|QueryBuilder
    {

        $withRelation = ['users'];
        $where = ['user_id' => $userId, 'type' => $type, 'status' => AppStatusEnum::CONFIRMED()];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('user_application_id')
        ];
        $allowedFields = [
            'id',
            'full_name',
            'passport',
            'type',
            'created_at',
            'updated_at'
        ];

        $allowedSorts = ['id', 'user_application_id'];

        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        return $this->personRepository->getAllByFiltersForUser(
            $where,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            [],
            $allowedSorts,
            false,
            $perPage
        );
    }


    /**
     * @param array $dataApp
     * @return ApiErrorResponse|Model|null
     * @throws CustomException
     */
    public function create(array $dataApp): ApiErrorResponse|Model|null
    {
        /**
         * Check user Application is confirmed
         */
        if (!$this->personRepository->checkUserAppStatus($dataApp['user_application_id'])) {
            throw new CustomException("Заявка с номером " . $dataApp['user_application_id'] . " не подтверждена", ResponseAlias::HTTP_FORBIDDEN);
        }

        $guest = $this->personRepository->create($dataApp);
        return $this->personRepository->findById($guest->id);
    }

    /**
     * @param int $id
     * @param int $userId
     * @param PersonTypesEnum $type
     * @return Collection|Model|QueryBuilder
     * @throws CustomException
     */
    public function show(int $id, int $userId, PersonTypesEnum $type)
    {

        try {
            $where = ['type' => $type, 'status' => AppStatusEnum::CONFIRMED()->value];
            $withRelation = ['users', 'user_applications'];
            return $this->personRepository->findByIdForUser($userId, $id, $withRelation, $where);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @param int $id
     * @param int $userId
     * @param array $dataApp
     * @return Model|null
     * @throws CustomException
     */
    public function update(int $id, int $userId, array $dataApp): Model|null
    {
        try {

            $withRelation = ['users', 'user_applications'];
            $updated = $this->personRepository->findByIdForUser($userId, $id, $withRelation);

            /**
             * Check user Application is confirmed
             */
            if (array_key_exists('user_application_id', $dataApp) && !$this->personRepository->checkUserAppStatus($dataApp['user_application_id'])) {
                throw new CustomException("Заявка с номерем " . $dataApp['user_application_id'] . " не подтверждена", ResponseAlias::HTTP_FORBIDDEN);
            }

            /**
             * If record with this id not existed or not available for current user
             */
            if (!$updated) {
                throw new CustomException("Ресурс с ID $id не найден", ResponseAlias::HTTP_BAD_REQUEST);
            }

            $this->personRepository->update($updated, $dataApp);
            return $this->personRepository->findById($id);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
