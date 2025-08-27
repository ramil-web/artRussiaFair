<?php

namespace App\Services;

use Admin\Http\Filters\EventCategoryFilter;
use App\Http\Filters\EventFilter;
use App\Http\Filters\NameFilter;
use App\Models\Partner;
use App\Repositories\Partner\PartnerRepository;
use Spatie\QueryBuilder\AllowedFilter;

class PartnerService
{

    public function __construct(
        private PartnerRepository $partnerRepository,
        public Partner            $partner)
    {
    }


    /**
     * @param mixed $dataApp
     * @return array
     */
    public function list(mixed $dataApp)
    {
        $withRelation = ['partnerCategory', 'eventgable'];
        $allowedFields = [
            'id',
            'sort_id',
            'important',
            'name',
            'partner_category_id',
            'image',
            'link',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        $allowedIncludes = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('partner_category_id'),
            AllowedFilter::exact('important'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::custom('event_id', new EventFilter()),
            AllowedFilter::custom('category', new EventCategoryFilter()),
        ];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'sort_id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        $response = $this->partnerRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->partner,
            $perPage,
            null,
            $page
        );
        return $this->modify($response);
    }

    /**
     * @param mixed $data
     * @return array
     */
    public function modify(mixed $data): array
    {
        $response = [];
        foreach ($data as $key => $val) {
            $response[$key]['id'] = $val['id'];
            $response[$key]['sort_id'] = $val['sort_id'];
            $response[$key]['name'] = $val['name'];
            $response[$key]['link'] = $val['link'];
            $response[$key]['image'] = $val['image'];
            $response[$key]['created_at'] = $val['created_at'];
            $response[$key]['updated_at'] = $val['updated_at'];
            $response[$key]['deleted_at'] = $val['deleted_at'];
            $response[$key]['important'] = $val['important'];
            $response[$key]['partner_category'] = [
                'id'         => $val->toArray()['partner_category']['id'],
                'name'       => $val->toArray()['partner_category']['name'],
                'created_at' => $val->toArray()['partner_category']['created_at'],
                'updated_at' => $val->toArray()['partner_category']['updated_at'],
            ];
            $response[$key]['eventgable'] = $val['eventgable'];
        }
        return $response;
    }
}
