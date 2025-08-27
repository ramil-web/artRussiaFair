<?php

namespace Admin\Services;

use Admin\Repositories\SchemaOfStand\SchemaOfStandRepository;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SchemaOfStandService
{

    public function __construct(public SchemaOfStandRepository $repository)
    {
    }

    const SCHEMA_OF_STAND_DIR = "schema-of-stand";

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id): bool
    {
        try {
            $this->removeImage();
            return $this->repository->deleteSchema($id);
        } catch (Throwable $e) {
            throw new CustomException(
                'Ошибка при удалении файлова, файл не существует',
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @return bool
     */
    protected function removeImage(): bool
    {
        $FileSystem = new Filesystem();
        $directory = storage_path() . '/app/uploads/' . self::SCHEMA_OF_STAND_DIR;
        if ($FileSystem->exists($directory) || !empty($directory)) {
            return $FileSystem->deleteDirectory($directory);
        }
    }

    /**
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(): Model|Collection|Builder|array|null
    {
        return $this->repository->show();
    }

    /**
     * @param array $appData
     * @return Model|Builder
     * @throws CustomException
     */
    public function store(array $appData): Model|Builder
    {
        return $this->repository->store($appData);
    }
}
