<?php

namespace App\Services;

use Admin\Repositories\AdminDocument\AdminDocumentRepository;
use App\Exceptions\CustomException;
use App\Exceptions\ImageUploadException;
use App\Jobs\File\DeleteFileJob;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;

class StorageService
{
    const SCHEMA_OF_STAND_DIR = "schema-of-stand";
    private string $path = 'uploads';
    public const ADMIN_DOCS_PATH = '/admin_docs/';

    public function __construct(protected AdminDocumentRepository $adminDocumentRepository)
    {
    }


    /**
     * @throws CustomException
     */
    public function upload($files, string $folder = ''): array
    {

        if (!$this->checkSchema()) {
            throw new CustomException(
                'Чтобы добавить новую схему, удалите существующую схему.',
                Response::HTTP_BAD_REQUEST
            );
        }

        $paths = [];
        foreach ($files as $file) {
            $parsedBase64 = $this->parseBase64($file);
            $folder = trim($folder, ' /');

            $relPath = sprintf(
                '%s%s.%s',
                $folder ? $folder . '/' : '',
                Str::random(20),
                $parsedBase64['ext']
            );

            $path = $this->getPath($relPath);
            if (!Storage::put($path, base64_decode($parsedBase64['content']))) {
                throw new RuntimeException('Ошибка при сохранении файла.');
            }
            $paths[] = $path;
        }
        return $paths;
    }

    /**
     * @return bool
     */
    private function checkSchema(): bool
    {
        $FileSystem = new Filesystem();
        $directory = storage_path() . '/app/uploads/' . self::SCHEMA_OF_STAND_DIR;
        if ($FileSystem->exists($directory)) {
            $files = $FileSystem->files($directory);
            return empty($files);
        } else {
            return true;
        }
    }

    /**
     * @param array $dataApp
     * @return mixed
     * @throws CustomException
     */
    public function uploadDoc(array $dataApp): mixed
    {
        try {
            $fileName = $dataApp['name'];
            Storage::put(self::ADMIN_DOCS_PATH . $fileName, file_get_contents($dataApp['file']));
            $path = self::ADMIN_DOCS_PATH . $fileName;
            return $this->adminDocumentRepository->create(
                $this->dataArray($path, $fileName, $dataApp['event_id'])
            );
        } catch (ImageUploadException $e) {
            throw new CustomException('Ошибка при сохранении файла.', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * TODO: Удалить если не понадобиться.
     * @param string $file
     * @param string $filepath
     * @return string|null
     * @throws \Exception
     */
    public function uploadFileByFilepath(string $file, string $filepath): ?string
    {
        $parsedBase64 = $this->parseBase64($file);

        if (!Storage::put($filepath, base64_decode($parsedBase64['content']))) {
            throw new \Exception('Error saving file');
        }

        return $filepath;
    }

    /**
     * Checking for availability file in storage by file path.
     *
     * @param string $filepath
     * @return bool
     */
    public function checkStorageFile(string $filepath): bool
    {
        return Storage::exists($filepath);
    }

    private function parseBase64(string $base64): array
    {
        $base64Parts = explode(';base64,', $base64);
        if (count($base64Parts) < 2) {
            throw new ImageUploadException();
        }

        $base64Content = end($base64Parts);
        $base64Meta = array_shift($base64Parts);

        $ext = explode('/', $base64Meta);
        $ext = end($ext);

        return [
            'ext'     => $ext,
            'content' => $base64Content
        ];
    }

    public function getPath($path = '')
    {
        return rtrim($this->path, '/') . '/' . ltrim($path, '/');
    }

    /**
     * @param array $dataApp
     * @return Collection|LengthAwarePaginator|array
     * @throws CustomException
     */
    public function getAdminDocs(array $dataApp): Collection|LengthAwarePaginator|array
    {
        $allowedFilters = [
            AllowedFilter::exact('event_id'),
        ];
        return $this->adminDocumentRepository->list($allowedFilters, $dataApp);
    }

    /**
     * @param int $id
     * @return string
     * @throws CustomException
     */
    public function deleteDoc(int $id): string
    {
        return $this->adminDocumentRepository->delete($id);
    }

    /**
     * @param string $path
     * @param mixed $fileName
     * @param mixed $eventId
     * @return array
     */
    private function dataArray(string $path, mixed $fileName, mixed $eventId): array
    {
        return [
            'path'     => $path,
            'name'     => $fileName,
            'event_id' => $eventId
        ];
    }

    /**
     * @param mixed $dataApp
     * @return Model
     * @throws CustomException
     */
    public function update(mixed $dataApp): Model
    {
        return $this->adminDocumentRepository->updateAdminDoc($dataApp);
    }

    /**
     * @throws FileNotFoundException
     */
    public function uploads(mixed $files, mixed $folder = ''): array
    {
        $paths = [];
        $filesArray = is_array($files) ? $files : [$files];
        foreach ($filesArray as $file) {
            $path = $this->getPath($folder);
            $filename = $file->getClientOriginalName();
            $fileName = $file->store($folder);
            if (!$filename) {
                throw new RuntimeException('Ошибка при сохранении файла.');
            }
            $paths[] = $path . '/' . substr($fileName, strpos($fileName, "/") + 1);
        }

        return $paths;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        if (!Storage::exists($path)) {
            return false;
        } else {
            DeleteFileJob::dispatch($path)->delay(now()->addMinutes(30));
            return true;
        }
    }
}
