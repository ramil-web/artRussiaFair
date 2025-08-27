<?php

namespace Lk\Repositories\MyDocument;

use App\Enums\AppStatusEnum;
use App\Exceptions\CustomException;
use App\Models\Contact;
use App\Models\MyDocument;
use App\Models\Requisite;
use App\Models\UserApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\BaseRepository;
use Lk\Services\MyDocumentsService;
use Storage;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MyDocumentsRepository extends BaseRepository
{
    public function __construct(MyDocument $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function createData(array $dataApp): Model
    {
        if (!$this->checkFileCount($dataApp)) {
            throw new CustomException("Не более 2-х файлов каждого типа", ResponseAlias::HTTP_BAD_REQUEST);
        }

        /**
         * If already existed with a same status update
         */
        if ($this->checkExistedWithSameStatus($dataApp['user_application_id'], $dataApp['status'])) {
            throw new CustomException(
                "Для каждого статуса вы можете загрузить документы 1 раз, можете редактировать документы в разделе редактирования",
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        /**
         * If already exists, but with a different status, remove existed and creat new
         */
        if ($this->checkExistedWithDifferentStatus($dataApp)) {
            if ($this->removeDocuments($dataApp['user_application_id'])) {
                return $this->addDocument($dataApp);
            }
        }

        /**
         * Create a new, if not existed yet
         */
        if (!$this->findByUserAppId($dataApp['user_application_id'])->exists()) {
            return $this->addDocument($dataApp);
        }
    }

    /**
     * @param array $dataApp
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    private function addDocument(array $dataApp): Model|Collection|Builder|array|null
    {
        try {
            $with = ['contacts'];
            $document = $this->createMyDocument($dataApp['user_application_id'], $dataApp['status'], $dataApp['files']);

            /**
             * In addition to the legal entity, there are banking details for everyone
             */
            if (!in_array($dataApp['status'], [MyDocumentsService::LEGAL_ENTITY, MyDocumentsService::SOLE_ENTREPRENEUR])) {
                $this->createRequisite($document->id, $dataApp);
                $with = ['contacts', 'requisites'];
            }
            $this->createContact($document->id, $dataApp);
            return $this->findDocument($document->id, $with);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $id
     * @param array $dataApp
     * @return Builder|Model
     * @throws CustomException
     */
    private function createContact($id, array $dataApp): Builder|Model
    {
        try {
            $data = [
                'my_document_id' => $id,
                'phone'          => $dataApp['phone'],
                'email'          => $dataApp['email'],
                'edo_operator'   => $dataApp['edo_operator'] ?? null,
                'edo_id'         => $dataApp['edo_id'] ?? null,
            ];
            return Contact::query()
                ->create($data);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws CustomException
     */
    private function createRequisite($id, array $dataApp): Builder|Model
    {
        try {
            $data = [
                'my_document_id'        => $id,
                'payment_account'       => $dataApp['payment_account'],
                'bank_name'             => $dataApp['bank_name'],
                'bic'                   => $dataApp['bic'],
                'correspondent_account' => $dataApp['correspondent_account'],
                'kpp'                   => $dataApp['kpp'],
                'inn'                   => $dataApp['inn'],
            ];
            return Requisite::query()
                ->create($data);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param mixed $user_application_id
     * @param mixed $status
     * @param mixed $files
     * @return Model
     * @throws CustomException
     */
    private function createMyDocument(mixed $user_application_id, mixed $status, mixed $files): Model
    {
        try {
            $data = [
                'user_application_id' => $user_application_id,
                'status'              => $status,
                'files'               => $files
            ];
            return $this->create($data);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @param mixed $id
     * @param array $with
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    private function findDocument(int $id, array $with): Model|Collection|Builder|array|null
    {
        try {
            return $this->model
                ->query()
                ->with($with)
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param mixed $userApplicationId
     * @param mixed $status
     * @return Model|Builder|null
     * @throws CustomException
     */
    private function checkExistedWithSameStatus(mixed $userApplicationId, mixed $status): Model|Builder|null
    {
        try {
            return $this->model
                ->query()
                ->where(['user_application_id' => $userApplicationId, 'status' => $status])
                ->first();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @param array $dataApp
     * @return Model|Builder|null
     * @throws CustomException
     */
    private function checkExistedWithDifferentStatus(array $dataApp): Model|Builder|null
    {
        try {
            return $this->model
                ->query()
                ->where('user_application_id', $dataApp['user_application_id'])
                ->whereNot('status', $dataApp['status'])
                ->first();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
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
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $dataApp
     * @return bool
     */
    private function checkFileCount(array $dataApp): bool
    {
        $count = [];
        $response = true;
        if (array_key_exists('files', $dataApp)) {
            foreach ($dataApp['files'] as $file) {
                $key = $file['type'];

                if (isset($count[$key])) {
                    $count[$key]++;
                } else {
                    $count[$key] = 1;
                }
            }
            foreach ($count as $key => $val) {
                if ($val > 2) {
                    $response = false;
                }
            }
        }
        return $response;
    }

    /**
     * @param array $dataApp
     * @return Builder|Model
     * @throws CustomException
     */
    public function updateMyDocuments(array $dataApp): Builder|Model
    {
        try {
            $with = ['contacts'];
            $this->updateDocuments($dataApp);

            /**
             * In addition to the legal entity, there are banking details for everyone
             */

            if (!in_array($dataApp['status'], [MyDocumentsService::LEGAL_ENTITY, MyDocumentsService::SOLE_ENTREPRENEUR])) {
                $this->updateRequisite($dataApp);
                $with = ['contacts', 'requisites'];
            }
            $this->updateContacts($dataApp);
            $where = ['user_application_id' => $dataApp['user_application_id'], 'status' => $dataApp['status']];
            return $this->getDocumentWithRelations($with, $where);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $userApplicationId
     * @return bool|null
     * @throws CustomException
     */
    private function removeDocuments($userApplicationId): bool|null
    {
        try {
            $documents = $this->findByUserAppId($userApplicationId)->firstOrFail();
            $files = $documents->toArray()['files'];
            $this->removeImage($files);
            return $this->model->query()->findOrFail($documents->id)->delete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $files
     * @return void
     */
    private function removeImage(array $files): void
    {
        if (!empty($files)) {
            foreach ($files as $file) {
                Storage::delete($file['url']);
            }
        }
    }


    /**
     * @param int $userApplicationId
     * @return Model|Builder|array
     * @throws CustomException
     */
    public function show(int $userApplicationId): Model|Builder|array
    {
        $with = ['contacts'];
        $where = ['user_application_id' => $userApplicationId];
        $documents = $this->findByUserAppId($userApplicationId);

        if (!$documents->exists()) {
            return [];
        }

        /**
         * In addition to the legal entity, there are banking details for everyone
         */
        if (!in_array($documents->first()->status, [MyDocumentsService::LEGAL_ENTITY, MyDocumentsService::SOLE_ENTREPRENEUR])) {
            $with = ['contacts', 'requisites'];
        }
        return $this->getDocumentWithRelations($with, $where);
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
                ->first();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @param $dataApp
     * @return bool|int
     * @throws CustomException
     */
    private function updateDocuments($dataApp): bool|int
    {
        try {
            $documents = $this->model->query()->findOrFail($dataApp['id']);

            /**
             * Проверяем ID заяки пренодложить авторизованныму участнику или нет
             */
            if (array_key_exists('user_application_id', $dataApp)) {
                $this->checkUserApp($dataApp['user_application_id']);
            }


            return $this->update($documents, $dataApp);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $dataApp
     * @return bool
     * @throws CustomException
     */
    public function deleteFile(array $dataApp): bool
    {
        try {
            $model = $this->model->query()->find($dataApp['id']);

            /**
             * Проверяем ID заяки пренодложить авторизованныму участнику или нет
             */
            if (!$this->checkUserApp($model->user_application_id)) {
                throw new CustomException('Заявления с таким номером не существуе', ResponseAlias::HTTP_BAD_REQUEST);
            }


            $files = $model->files ?? [];
            $data['files'] = [];
            foreach ($files as $file) {
                if ($dataApp['url'] == $file['url'] && $dataApp['name'] == $file['name'] && $dataApp['type'] == $file['type']) {
                    Storage::delete($dataApp['url']);
                } else {
                    $data['files'][] = $file;
                }
            }
            $data['files'] = empty($data['files']) ? [json_encode([])] : $data['files'];
            return $this->update($model, $data);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @param array $dataApp
     * @return Builder|Model|bool
     * @throws CustomException
     */
    private function updateRequisite(array $dataApp): Builder|Model|bool
    {
        try {
            $requisite = Requisite::query()
                ->where('my_document_id', $dataApp['id']);
            if ($requisite->exists()) {
              return  $requisite->first()->update($dataApp);
            } else{
              return  $this->createRequisite($dataApp['id'], $dataApp);
            }
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $dataApp
     * @return bool|int
     * @throws CustomException
     */
    private function updateContacts($dataApp): bool|int
    {
        try {
            $requisite = Contact::query()
                ->where('my_document_id', $dataApp['id'])
                ->firstOrFail();
            return $requisite->update($dataApp);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $userApplicationId
     * @return bool
     * @throws CustomException
     * @throws CustomException
     */
    public function checkUserApp(int $userApplicationId): bool
    {
        $myAppIds = [];

        foreach (auth()->user()->userApplications as $app) {
            $myAppIds[] = $app->id;
        }

        if (!in_array($userApplicationId, $myAppIds)) {
            throw new CustomException('Заявления с таким номером не существуе', ResponseAlias::HTTP_BAD_REQUEST);
        }

        $userApp = UserApplication::query()
            ->findOrFail($userApplicationId);
        if ($userApp->status !== AppStatusEnum::CONFIRMED()->value) {
            throw new CustomException(
                "A confirmed application with this number was not found",
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
        return true;
    }

    /**
     * @param int $userApplicationId
     * @return bool
     */
    public function checkUserAppIsOwn(int $userApplicationId): bool
    {
        $myAppIds = [];

        foreach (auth()->user()->userApplications as $app) {
            $myAppIds[] = $app->id;
        }

        if (!in_array($userApplicationId, $myAppIds)) {
            return false;
        }

        $userApp = UserApplication::query()
            ->find($userApplicationId);
        if ($userApp->status !== AppStatusEnum::CONFIRMED()->value) {
            return false;
        }
        return true;
    }
}
