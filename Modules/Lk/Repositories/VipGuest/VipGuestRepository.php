<?php

namespace Lk\Repositories\VipGuest;

use App\Exceptions\CustomException;
use App\Models\VipGuest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VipGuestRepository extends BaseRepository
{

    public function __construct(VipGuest $model)
    {
        parent::__construct($model);
    }

    public function create(array $Data): Model
    {
        $locale = $Data['locate'] ?? app()->getLocale();

        $translate = config('transletable.vipguest');
        foreach ($translate as $value) {
            $Data[$value] = [$locale => $Data[$value]];
        }
        return $this->model->create($Data);
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function remove(int $id): bool
    {
        try {
            $userApplicationIds = $this->getUserAppIds();
            $model = $this->model->query()
                ->where('id', $id)
                ->whereIn('user_application_id', $userApplicationIds)
                ->firstOrFail();
            return $this->forceDelete($model);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return array
     */
    private function getUserAppIds(): array
    {
        $response = [];
        foreach (auth()->user()->userApplications as $app) {
            $response[] = $app->id;
        }
        return $response;
    }
}
