<?php

namespace Admin\Services;

use Admin\Repositories\Chat\ChatRepository;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

class ChatService
{

    public const LIMIT = 200;
    public function __construct(protected ChatRepository $chatRepository)
    {
    }

    /**
     * @param array $appData
     * @return Collection|array
     * @throws CustomException
     */
    public function chatParticipants(array $appData): Collection|array
    {
        return $this->chatRepository->chatParticipants($appData);
    }

    /**
     * @param array $appData
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function getMessages(array $appData): Model|Collection|Builder|array|null
    {
        return $this->chatRepository->messages($appData);
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
        return $this->chatRepository->deleteData($id);
    }

    /**
     * @throws CustomException
     */
    public function createMessage(array $appData)
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
     * @param int $chatRoomId
     * @return Collection|array
     * @throws CustomException
     */
    public function getMessageList(int $chatRoomId): Collection|array
    {
        return $this->chatRepository->messageList($chatRoomId);
    }
}
