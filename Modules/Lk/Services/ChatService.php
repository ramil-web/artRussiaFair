<?php

namespace Lk\Services;

use App\Exceptions\CustomException;
use Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\Chat\ChatRepository;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ChatService
{

    public const LIMIT = 200;
    public function __construct(protected ChatRepository $chatRepository)
    {
    }

    /**
     * @param mixed $appData
     * @return Model
     * @throws CustomException
     */
    public function createMessage(mixed $appData): Model
    {
        if (!array_key_exists('message', $appData) && !array_key_exists('file', $appData)) {
            throw  new CustomException(
                "The letter must contain at least one character or file",
                Response::HTTP_BAD_REQUEST
            );
        }
        return $this->chatRepository->createMessage($appData);
    }

    /**
     * @return Collection|array
     * @throws CustomException
     */
    public function getMessage(): Collection|array
    {
        Cache::put('record_limit', self::LIMIT);
        Cache::put('skip', self::LIMIT);
        return $this->chatRepository->getMessages();
    }

    /**
     * @param int $id
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        return $this->chatRepository->show($id);
    }

    /**
     * @param array $appData
     * @return array|Builder|Collection|Model
     * @throws CustomException
     */
    public function update(array $appData): array|Builder|Collection|Model
    {
        return $this->chatRepository->updateMessage($appData);
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function delete(int $id): mixed
    {
        return $this->chatRepository->deleteMessage($id);
    }

    /**
     * @return Model|QueryBuilder
     * @throws CustomException
     */
    public function manager(): Model|QueryBuilder
    {
        return $this->chatRepository->manager();
    }

    /**
     * @param array $appData
     * @return Collection|array
     * @throws CustomException
     */
    public function searchMessage(array $appData): Collection|array
    {
        return $this->chatRepository->searchMessage($appData);
    }

    /**
     * @param array $appData
     * @return bool
     * @throws CustomException
     */
    public function status(array $appData): bool
    {
        return $this->chatRepository->status($appData);
    }

    /**
     * @param string $email
     * @return Builder|Model
     * @throws CustomException
     */
    public function getChatRoomId(string $email): Builder|Model
    {
        return $this->chatRepository->getChatRoomId($email);
    }

    /**
     * @return Collection|array
     * @throws CustomException
     */
    public function getMessageList(): Collection|array
    {
        return $this->chatRepository->messageList();
    }
}
