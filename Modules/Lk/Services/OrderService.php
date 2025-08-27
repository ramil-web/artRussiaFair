<?php

namespace Lk\Services;

use App\Enums\OrderItemTypesEnum;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\Order\OrderRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderService
{

    public function __construct(
        public OrderRepository $orderRepository
    ){}

    /**
     * @param int $id
     * @param array $dataApp
     * @return Collection
     * @throws CustomException
     */
    public function list(int $id, array $dataApp): Collection
    {
        $withRelation = ['users'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('user_application_id'),
            AllowedFilter::exact('time_slot_start_id'),
            AllowedFilter::exact('time_slot_end_id'),
            AllowedFilter::exact('stand_area'),
            AllowedFilter::exact('status'),
        ];
        $allowedFields = ['id', 'user_application_id', 'status', 'time_slot_start','time_slot_end', 'stand_area', 'created_at', 'updated_at'];
        $allowedIncludes = [];
        $where = ['user_id' => $id];
        $allowedSorts = ['id', 'status', 'created_at', 'updated_at'];
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;

        $response = $this->orderRepository->getAllByFiltersForUser(
            $where,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            false,
            $perPage
        );

        foreach ($response as $key => $value) {
            unset($response[$key]->ordr_items);
            unset($response[$key]->users);
        }
        return $response;
    }


    /**
     * @param int $id
     * @param int $userId
     * @param mixed $dataApp
     * @return Model|null
     * @throws CustomException
     */
    public function update(int $id, int $userId, array $dataApp): Model|null
    {
        try {
            $withRelation = ['users', 'user_applications'];
            $updated = $this->orderRepository->findByIdForUser($userId, $id, $withRelation);
            if (!$updated) {
                throw new CustomException("Ресурс с ID $id не найден", ResponseAlias::HTTP_BAD_REQUEST);
            }
            $this->orderRepository->update($updated, $dataApp);
            $response = $this->orderRepository->findById($id, $withRelation);
            return $this->modifyOrderResponse($response);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param int $userId
     * @return Collection|Model|QueryBuilder|QueryBuilder[]|null
     * @throws CustomException
     */
    public function show(int $id, int $userId): Model|Collection|QueryBuilder|array|null
    {
        try {
            $withRelation = ['users', 'user_applications', 'time_slot_start', 'time_slot_end','order_items'];
            return $this->orderRepository->findByIdForUser($userId, $id, $withRelation);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }


    public function modifyOrderResponse($response)
    {
        $additionalServices = [];
        $hardware = [];
        foreach ($response->order_items as  $key => $item) {
            if ($item->type == OrderItemTypesEnum::HARDWARE()) {
                $hardware[] = $item;
                unset($response->order_items[$key]->image);
            }
            if ($item->type == OrderItemTypesEnum::ADDITIONAL_SERVICE()) {
                $additionalServices[] = $item;
                unset($response->order_items[$key]->article);
            }

        }
        $response->additional_services = $additionalServices;
        $response->hardware = $hardware;
        unset($response->order_items);
        return $response;
    }

}
