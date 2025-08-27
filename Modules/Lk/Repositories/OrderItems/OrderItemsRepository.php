<?php

namespace Lk\Repositories\OrderItems;

use App\Enums\AppStatusEnum;
use App\Exceptions\CustomException;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\BaseRepository;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Класс общий для допуслуг и Оборудование на заказ. По сути они и есть order_items
 */
class OrderItemsRepository extends BaseRepository
{


    public function __construct(OrderItem $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }

    public function create(array $Data): Model
    {
        return $this->model->create($Data);
    }

    public function update(Model $model, array $Data): bool
    {
        return $model->update($Data);
    }

    /**
     * @throws CustomException
     */
    public function getAllByFilters(
        array $where = [],
        array $withRelation = [],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        bool  $withTrashed = false,
        int   $perPage = null,
    )
    {
        try {
            $query = QueryBuilder::for($this->model)
                ->with($withRelation)
                ->allowedFilters($allowedFilters)
                ->allowedFields($allowedFields)
                ->allowedIncludes($allowedIncludes)
                ->where('type', $where['type'])
                ->defaultSort('id')
                ->allowedSorts($allowedSorts);

            if ($withTrashed) {
                $query->withTrashed();
            }

            $id = $where['user_id'];

            /**
             * Проверяем участинка по ID
             */
            $query->whereHas('user_applications', function ($q) use ($id) {
                $q->where('user_id', $id);
            });

            return $perPage !== null
                ? $query->jsonPaginate($perPage)
                : $query->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @param array $dataApp
     * @return void
     * @throws CustomException
     */
    public final function checkUserApp(array $dataApp): void
    {

        try {
            $orderExists = Order::query()->where('id', $dataApp['order_id'])->exists();
            if (!$orderExists) {
                throw new CustomException("Заказ с номерем " . $dataApp['order_id'] . " не найден", ResponseAlias::HTTP_BAD_REQUEST);
            }

            $userApplicationInfo = Order::query()
                ->with(['user_applications'])
                ->where('id', $dataApp['order_id'])
                ->firstOrFail();


            /**
             * Check user Application is confirmed
             */
            if ($userApplicationInfo->user_applications->status != AppStatusEnum::CONFIRMED()->value) {
                throw new CustomException("Заявка с номером " . $userApplicationInfo->user_applications->id . " не подтверждена", ResponseAlias::HTTP_FORBIDDEN);
            }

            /**
             * Check user Application available for current user
             */
            if ($userApplicationInfo->user_applications->user_id != $dataApp['user_id']) {
                throw new CustomException("Текущий пользователь не имеет доступа к указанной заявке.", ResponseAlias::HTTP_FORBIDDEN);
            }

        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }


    }
}
