<?php

namespace Lk\Services;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\UserApplication\UserApplicationRepository;
use Lk\Repositories\VipGuest\VipGuestRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VipGuestService
{

    /**
     * @param VipGuestRepository $vipGuestRepository
     * @param UserApplicationRepository $userApplicationRepository
     */
    public function __construct(
        public VipGuestRepository        $vipGuestRepository,
        public UserApplicationRepository $userApplicationRepository
    )
    {
    }

    /**
     * @throws CustomException
     */
    public function list(int $userId, array $dataApp)
    {
        $withRelation = ['users'];
        $where = ['user_id' => $userId];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('user_application_id')
        ];
        $allowedFields = [
            'id',
            'full_name',
            'user_application_id',
            'organization',
            'email',
            'created_at',
            'updated_at'
        ];

        $allowedSorts = ['id', 'user_application_id'];

        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        return $this->vipGuestRepository->getAllByFiltersForUser(
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
     * @return Model
     * @throws CustomException
     */
    public function create(array $dataApp): Model
    {
        /**
         * Check user Application is confirmed
         */
        if (!$this->vipGuestRepository->checkUserAppStatus($dataApp['user_application_id'])) {
            throw new CustomException("Заявка с номером " . $dataApp['user_application_id'] . " не подтверждена", ResponseAlias::HTTP_FORBIDDEN);
        }
        $guest = $this->vipGuestRepository->create($dataApp);
        return $this->vipGuestRepository->findById($guest->id);
    }


    /**
     * @param int $id
     * @param int $userId
     * @return Model
     * @throws CustomException
     */
    public function show(int $id, int $userId): Model
    {
        try {
            $withRelation = ['users', 'user_applications'];
            return $this->vipGuestRepository->findByIdForUser($userId, $id, $withRelation);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param int $userId
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function update(int $id, int $userId, array $dataApp): Model
    {

        try {

            $withRelation = ['users', 'user_applications'];
            $updated = $this->vipGuestRepository->findByIdForUser($userId, $id, $withRelation);

            /**
             * Check user Application is confirmed
             */
            if (array_key_exists('user_application_id', $dataApp) && !$this->vipGuestRepository->checkUserAppStatus($dataApp['user_application_id'])) {
                throw new CustomException("Заявка с номерем " . $dataApp['user_application_id'] . " не подтверждена", ResponseAlias::HTTP_FORBIDDEN);
            }

            /**
             * If record with this id not existed or not available for current user
             */
            if (!$updated) {
                throw new CustomException("Ресурс с ID $id не найден", ResponseAlias::HTTP_BAD_REQUEST);
            }

            $this->vipGuestRepository->update($updated, $dataApp);
            return $this->vipGuestRepository->findById($id);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id): bool
    {
        return $this->vipGuestRepository->remove($id);
    }
}
