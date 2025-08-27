<?php

namespace Admin\Repositories\MyDocument;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\MyDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Services\MyDocumentsService;
use Symfony\Component\HttpFoundation\Response;

class MyDocumentRepository extends BaseRepository
{
    public function __construct(MyDocument $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int $userApplicationId
     * @return Model|Builder
     * @throws CustomException
     */
    public function show(int $userApplicationId): Model|Builder
    {
        $with = ['contacts'];
        $where = ['user_application_id' => $userApplicationId];
        $documents = $this->findByUserAppId($userApplicationId)->firstOrFail();

        /**
         * In addition to the legal entity, there are banking details for everyone
         */
        if (!in_array($documents->status, [MyDocumentsService::LEGAL_ENTITY, MyDocumentsService::SOLE_ENTREPRENEUR])) {
            $with = ['contacts', 'requisites'];
        }
        return $this->getDocumentWithRelations($with, $where);
    }

    /**
     * @param int $userApplicationId
     * @return Builder
     * @throws CustomException
     */
    public function findByUserAppId(int $userApplicationId): Builder
    {
        try {
            return $this->model
                ->query()
                ->where('user_application_id', $userApplicationId);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $with
     * @param array $where
     * @return Model|Builder
     * @throws CustomException
     */
    private function getDocumentWithRelations(array $with, array $where): Model|Builder
    {
        try {
            return $this->model
                ->query()
                ->with($with)
                ->where($where)
                ->firstOrFail();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return Collection|array
     * @throws CustomException
     */
    public function list(): Collection|array
    {
        try {
            $with = ['contacts', 'requisites'];
            return $this->model
                ->query()
                ->with($with)
                ->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
