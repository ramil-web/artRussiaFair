<?php

namespace Admin\Repositories\AdminDocument;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Exceptions\ImageUploadException;
use App\Models\AdminDocument;
use App\Services\StorageService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class AdminDocumentRepository extends BaseRepository
{
    public function __construct(AdminDocument $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $allowedFilters
     * @param $dataApp
     * @return Collection|LengthAwarePaginator|array
     * @throws CustomException
     */
    public function list(array $allowedFilters, $dataApp): Collection|LengthAwarePaginator|array
    {
        try {
            $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'id';
            $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
            $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : 20;
            $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : 1;
            $pageName = 'page';
            $allowedFields = ['id', 'event_id', 'name', 'path', 'created_at', 'updated_at'];
            $query = QueryBuilder::for($this->model);
            $query = $query->allowedFilters($allowedFilters);

            $order = strtolower($orderBy) == self::DESC ? '-' : '';
            $query = $query->defaultSort($order . $sortBy);

            return $query->paginate($perPage, $allowedFields, $pageName, $page);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $id
     * @return string
     * @throws CustomException
     */
    public function delete(string $id): string
    {
        try {
            $file = $this->model
                ->query()
                ->findOrFail($id);
            $path = StorageService::ADMIN_DOCS_PATH . $file->name;
            if (Storage::delete($path)) {
                $file->delete();
            }
            return "Файл успешно удален!";
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function updateAdminDoc(array $dataApp): Model
    {
        try {
            $file = $this->model
                ->query()
                ->find($dataApp['id']);

            if (array_key_exists('file', $dataApp)) {
                if (Storage::delete($file->path)) {
                    $path = array_key_exists('path', $dataApp) ? $dataApp['path'] : $file->path;
                    $name = array_key_exists('name', $dataApp) ? $dataApp['name'] : $file->name;
                    $eventId = array_key_exists('event_id', $dataApp) ? $dataApp['event_id'] : $file->event_id;
                    Storage::put(StorageService::ADMIN_DOCS_PATH . $name, file_get_contents($dataApp['file']));
                    $file->update([
                        'path' => $path,
                        'name' => $name,
                        'event_id' => $eventId
                    ]);
                }
            } else {
                if (array_key_exists('name', $dataApp)) {
                    Storage::move(StorageService::ADMIN_DOCS_PATH.$file->name, StorageService::ADMIN_DOCS_PATH.$dataApp['name']);
                }
                $file->update($dataApp);
            }
            return $file;
        } catch (ImageUploadException $e) {
            throw new CustomException('Ошибка при сохранении файла.', Response::HTTP_BAD_REQUEST);
        }
    }


}
