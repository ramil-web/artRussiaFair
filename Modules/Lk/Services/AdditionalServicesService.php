<?php

namespace Lk\Services;

use App\Enums\OrderItemTypesEnum;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\OrderItems\OrderItemsRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AdditionalServicesService
{

    private $type;

    public function __construct(
        public OrderItemsRepository $orderItemRepository
    ){
        $this->type = OrderItemTypesEnum::ADDITIONAL_SERVICE();
    }

    /**
     * @param array $dataApp
     * @return Model|null
     * @throws CustomException
     */
    public function create(array $dataApp): Model|null
    {
        try {

            /**
             * Check user Application confirmed and available for curren user
             */
            $this->orderItemRepository->checkUserApp($dataApp);

            $orderItem = $this->orderItemRepository->create($dataApp);
            $response =  $this->orderItemRepository->findById($orderItem->id);
            unset($response->product_id);
            return $response;
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param array $dataApp
     * @return Collection|QueryBuilder[]
     * @throws CustomException
     */
    public function list(int $id, array $dataApp)
    {
        $withRelation = ['user_applications'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('order_id'),
            AllowedFilter::exact('type'),
        ];
        $allowedFields = ['id', 'quantity',  'type', 'order_id', 'created_at', 'updated_at'];
        $allowedIncludes = [];
        $where = ['user_id' => $id, 'type' => OrderItemTypesEnum::ADDITIONAL_SERVICE()];
        $allowedSorts = ['id', 'order_id', 'created_at', 'updated_at'];
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;

        $response = $this->orderItemRepository->getAllByFilters(
            $where,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            false,
            $perPage
        );
        /**
         * Удаляем данные заявок
         */
        foreach ($response as $key => $value) {
            unset($response[$key]->user_applications);
            unset($response[$key]->product_id);
        }
        return $response;
    }

    /**
     * @param int $id
     * @param int $userId
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function update(int $id, int $userId,array $dataApp): Model
    {
        try {
            $withRelation = ['orders', 'user_applications'];

            /**
             * Check userApplication confirmed and available for user
             */
            if(array_key_exists('order_id', $dataApp)) {
                $dataApp['user_id'] = $userId;
                $this->orderItemRepository->checkUserApp($dataApp);
            }

            $updated = $this->orderItemRepository->findByIdForUserByType($this->type, $userId, $id);
            if(!$updated) {
                throw new CustomException("Ресурси с ID $id не найден!", ResponseAlias::HTTP_NOT_FOUND);
            }
            $this->orderItemRepository->update($updated, $dataApp);
            $response = $this->orderItemRepository->findById($id, $withRelation);
            unset($response->article);
            return  $response;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(),  ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param int $userId
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function show(int $id, int $userId, array $dataApp): Model
    {
        try {
            $withRelation = ['user_applications', 'service_catalogs', 'orders'];
            $allowedFields = ['id',  'service_catalog_id', 'order_id', 'created_at', 'updated_at'];
            $allowedIncludes = [];
            $withTrashed = array_key_exists('with_trashed', $dataApp) ? $dataApp['with_trashed'] : false;
            $response =  $this->orderItemRepository->findByIdForUserByType($this->type, $userId, $id, $withRelation, $allowedFields, $allowedIncludes, $withTrashed);

            if(!$response) {
                throw new CustomException("Ресурс с ID $id не найден", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
            }
            unset($response->user_applications);
            unset($response->product_id);
            return $response;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
