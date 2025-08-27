<?php

namespace App\Repositories\Event;

use App\Exceptions\CustomException;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class EventRepository implements EventRepositoryInterface
{
    public function __construct(protected Event $model)
    {
    }

    /**
     * @throws CustomException
     */
    public function get(int $id): Collection|Model|QueryBuilder|array
    {
        try {
            return $this->model
                ->query()
                ->with('time_slots')
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $appData
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|QueryBuilder[]
     * @throws CustomException
     */
    public function list(array $appData): \Illuminate\Database\Eloquent\Collection|array
    {

        try {
            $query = QueryBuilder::for($this->model);
            /**
             * Фильтрует события,получает только привязанные к партнерам
             */
            if (array_key_exists('has_partners', $appData)) {
                $query = $query->whereHas('partners', function ($query) {
                })->with(['partners']);
            }

            /**
             * Фильтрует события по годам
             */
            if (array_key_exists('year', $appData)) {
                $query = $query->where('year', $appData['year']);
            }

            /**
             * Фильтрует события по категорию партнера
             */
            if (array_key_exists('partner_category_id', $appData)) {
                $query = $query->with(['partners'])->whereHas('partners', function ($query) use ($appData) {
                    return $query->where('partners.partner_category_id', $appData['partner_category_id']);
                });
            }

            return $query
                ->allowedFilters([
                    AllowedFilter::exact('category')
                ])
                ->orderBy('id')->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
