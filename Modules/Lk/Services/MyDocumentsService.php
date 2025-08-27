<?php

namespace Lk\Services;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\MyDocument\MyDocumentsRepository;
use Storage;

class MyDocumentsService
{


    const LEGAL_ENTITY = 'legal_entity';
    const AGREEMENT_DIR = 'agreement';
    const  SOLE_ENTREPRENEUR = 'sole_entrepreneur';

    public function __construct(protected MyDocumentsRepository $documentsRepository)
    {
    }

    /**
     * @param array $dataApp
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function create(array $dataApp): Model|Collection|Builder|array|null
    {
        return $this->documentsRepository->createData($dataApp);
    }

    /**
     * @param array $dataApp
     * @return Model|Builder
     * @throws CustomException
     */
    public function update(array $dataApp): Model|Builder
    {
        return $this->documentsRepository->updateMyDocuments($dataApp);
    }

    /**
     * @param int $userApplicationId
     * @return Model|Builder|array
     * @throws CustomException
     */
    public function show(int $userApplicationId): Model|Builder|array
    {
        return $this->documentsRepository->show($userApplicationId);
    }

    /**
     * @param array $dataApp
     * @return bool
     * @throws CustomException
     */
    public function deleteFile(array $dataApp): bool
    {
        return $this->documentsRepository->deleteFile($dataApp);
    }

    /**
     * @return array
     */
    public function getAgreementFile(): array
    {
        $files = Storage::allFiles(self::AGREEMENT_DIR);
        $response = [];
        foreach ($files as $file) {
            $response[] = storage_path('/app/' . $file);
        }
        return $response;
    }

    /**
     * @param int $userApplicationId
     * @return bool
     * @throws CustomException
     */
    public function checkUserApp(int $userApplicationId): bool
    {
        return $this->documentsRepository->checkUserApp($userApplicationId);
    }

    /**
     * @param int $userApplicationId
     * @return bool
     */
    public function checkUserAppIsOwn(int $userApplicationId): bool
    {
        return $this->documentsRepository->checkUserAppIsOwn($userApplicationId);
    }
}
