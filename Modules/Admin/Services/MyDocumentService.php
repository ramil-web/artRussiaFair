<?php

namespace Admin\Services;

use Admin\Repositories\MyDocument\MyDocumentRepository;
use App\Exceptions\CustomException;
use App\Exceptions\ImageUploadException;
use App\Models\MyDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MyDocumentService
{
    public function __construct(
        protected MyDocumentRepository $documentRepository,
        protected MyDocument           $myDocument
    )
    {
    }

    const AGREEMENT_DIR = 'agreement';

    /**
     * @param string $file
     * @param string $name
     * @return string[]
     * @throws CustomException
     */
    public function upload(string $file, string $name): array
    {
        try {
            $path = '/' . self::AGREEMENT_DIR . '/' . $name;
            Storage::put($path, file_get_contents($file));
            return ['url' => storage_path('/app/' . self::AGREEMENT_DIR . '/' . $name)];
        } catch (ImageUploadException $e) {
            throw new CustomException('Ошибка при сохранении файла.', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return array
     * @throws CustomException
     */
    public function getFiles(): array
    {
        try {
            $files = Storage::allFiles(self::AGREEMENT_DIR);
            $response = [];
            foreach ($files as $file) {
                $response[] = [
                    'name' => substr(strrchr($file, '/'), 1),
                    'url'  => storage_path('/app/' . $file)
                ];
            }
            return $response;
        } catch (Throwable $e) {
            throw new CustomException('Ошибка при олучение файлов.', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $name
     * @return bool
     * @throws CustomException
     */
    public function delete(string $name): bool
    {
        $path = '/' . self::AGREEMENT_DIR . '/' . $name;
        try {
            if (!Storage::exists($path)) {
                throw new CustomException('Файл с таким называнием не существует.', Response::HTTP_BAD_REQUEST);
            }
            return Storage::delete($path);
        } catch (Throwable $e) {
            throw new CustomException(
                'Ошибка при удалении файлова, файл с таким называнием не существует',
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param int $userApplicationId
     * @return Model|Builder
     * @throws CustomException
     */
    public function show(int $userApplicationId): Model|Builder
    {
        return $this->documentRepository->show($userApplicationId);
    }

    /**
     * @param array $dataApp
     * @return Collection|array|LengthAwarePaginator
     */
    public function list(array $dataApp): Collection|array|LengthAwarePaginator
    {

        $withRelation = ['contacts', 'requisites'];
        $allowedFields = [
            'id',
            'user_application_id',
            'status',
            'files',
            'created_at',
            'updated_at'
        ];
        $allowedIncludes = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('user_application_id'),
        ];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        return $this->documentRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->myDocument,
            $perPage,
            null,
            $page
        );
    }
}
