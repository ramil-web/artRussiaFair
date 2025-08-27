<?php

namespace Admin\Services;


use Admin\Repositories\Order\OrderRepository;
use App\Enums\OrderItemTypesEnum;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderService
{

    public function __construct(
        public OrderRepository $orderRepository
    )
    {
    }

    /**
     * @param array $dataApp
     * @return Collection
     * @throws CustomException
     */
    public function list(array $dataApp): Collection
    {
        $withRelation = ['user_applications', 'time_slot_start', 'order_items'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('user_application_id'),
            AllowedFilter::exact('time_slot_start_id'),
            AllowedFilter::exact('status'),
        ];
        $allowedFields = ['id', 'user_application_id', 'status', 'time_slot_start', 'order_items', 'created_at', 'updated_at'];
        $allowedIncludes = [];
        $allowedSorts = ['id', 'status', 'created_at', 'updated_at'];
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;

        $response = $this->orderRepository->getAllByFilters(
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            $perPage,
        );
        foreach ($response as &$val) {
          $this->modifyOrderResponse($val);
        }
        return $response;
    }

    /**
     * @param int $id
     * @param mixed $dataApp
     * @return Model
     * @throws CustomException
     */
    public function update(int $id, mixed $dataApp): Model
    {
        try {
            $this->orderRepository->chekSlotCount($id, $dataApp);
            $this->orderRepository->update($this->orderRepository->findById($id), $dataApp);
            return $this->orderRepository->findById($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return mixed
     * @throws CustomException
     */
    public function show(int $id): mixed
    {
        try {
            $withRelation = ['user_applications', 'time_slot_start', 'order_items'];
            $allowedFields = ['id', 'user_application_id', 'status', 'time_slot_start', 'created_at', 'updated_at'];
            $allowedIncludes = [];
            $response = $this->orderRepository->findById($id, $withRelation, $allowedFields, $allowedIncludes);

            if (!$response) {
                throw new CustomException("Ресурс с ID $id не найден", ResponseAlias::HTTP_NOT_FOUND);
            }
            $response = $this->modifyOrderResponse($response);
            unset($response->user_applications);
            unset($response->time_slot_start);
            return $response;
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
