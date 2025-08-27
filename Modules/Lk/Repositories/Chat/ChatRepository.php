<?php

namespace Lk\Repositories\Chat;

use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use App\Services\StorageService;
use Auth;
use Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Lk\Repositories\BaseRepository;
use Lk\Services\ChatService;
use Respect\Validation\Exceptions\FileException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ChatRepository extends BaseRepository
{

    const CHAT_FILES = 'chat_files';

    public function __construct(
        ChatMessage              $model,
        protected User           $user,
        protected StorageService $storageService
    )
    {
        parent::__construct($model);
    }

    /**
     * @param array $appData
     * @return Model
     * @throws CustomException
     */
    public function createMessage(array $appData): Model
    {
        try {
            $user = Auth::user();
            if (array_key_exists('file', $appData) && !is_null($appData['file'])) {
                $filePath = $this->upload($appData);
            }

            $chatRoomId = array_key_exists('chat_room_id', $appData)
                ? $appData['chat_room_id']
                : $this->checkChatRoomId();

            $data = [
                'user_id'      => $user->id,
                'chat_room_id' => $chatRoomId,
                'message'      => $appData['message'] ?? null,
                'file_name'    => $appData['file_name'] ?? null,
                'file_path'    => $filePath ?? null
            ];

            return $this->create($data);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $appData
     * @return mixed
     * @throws CustomException
     */
    private function upload(array $appData): mixed
    {
        try {
            $file = [$appData['file']];
            $path = $this->storageService->upload($file, self::CHAT_FILES);
            return $path[0];
        } catch (FileException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return HigherOrderBuilderProxy|mixed
     * @throws CustomException
     */
    private function checkChatRoomId(): mixed
    {
        try {
            $userEmail = Auth::user()->email;
            $chatRoom = ChatRoom::query()
                ->where('name', $userEmail);
            if ($chatRoom->exists()) {
                return $chatRoom->firstOrFail()->id;
            } else {
                $created = ChatRoom::query()
                    ->create(['name' => $userEmail]);
                return $created->id;
            }
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws CustomException
     */
    public function getMessages(int $limit = ChatService::LIMIT, $skip = 0): Collection|array
    {
        try {
            $userEmail = Auth::user()->email;
            $chatRoomId = $this->roomId($userEmail);

            if (!$chatRoomId) {
                return ["You haven't written anything yet"];
            }

            return $this->model
                ->query()
                ->where('chat_room_id', $chatRoomId)
                ->with(['user'])
                ->orderBy('created_at', 'ASC')
                ->skip($skip)
                ->take($limit)
                ->get();
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $userEmail
     * @return HigherOrderBuilderProxy|mixed
     * @throws CustomException
     */
    private function roomId($userEmail): mixed
    {
        try {
            $chatRoom = ChatRoom::query()
                ->where('name', $userEmail)
                ->first();
            return $chatRoom ? $chatRoom->id : false;
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        try {
            return $this->model
                ->query()
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function deleteMessage(int $id): mixed
    {
        try {
            $userId = Auth::user()->id;
            $model = $this->model
                ->query()
                ->findOrFail($id);
            if ($model->user_id != $userId) {
                throw  new CustomException("You can only delete your messages", Response::HTTP_BAD_REQUEST);
            } else {

                /**
                 * Delete file not only path from DB
                 */
                if ($model->file_path) {
                    Storage::delete($model->file_path);
                }
                return $model->delete();
            }
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $appData
     * @return array|Builder|Collection|Model
     * @throws CustomException
     */
    public function updateMessage(array $appData): array|Builder|Collection|Model
    {
        try {
            $userId = Auth::user()->id;
            $model = $this->model
                ->query()
                ->where(['id' => $appData['id'], 'chat_room_id' => $appData['chat_room_id']])
                ->firstOrFail();
            if ($model->user_id != $userId) {
                throw  new CustomException("You can only update your messages", Response::HTTP_BAD_REQUEST);
            } else {
                $this->update($model, $appData);
                return $this->show($model->id);
            }
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return Model|QueryBuilder
     * @throws CustomException
     */
    public function manager(): Model|QueryBuilder
    {
        try {
            $withRelation = ['roles', 'managerProfile'];
            $role = [UserRoleEnum::SUPER_ADMIN];
            $query = QueryBuilder::for($this->user);
            $query = count($role) > 0 ? $query->role($role) : $query;
            $query = $query->with($withRelation);
            return $query->firstOrFail();
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $appData
     * @return Collection|array
     * @throws CustomException
     */
    public function searchMessage(array $appData): Collection|array
    {
        try {
            return $this->model
                ->query()
                ->where('chat_room_id', $appData['chat_room_id'])
                ->where("message", 'ILIKE', "%{$appData['message']}%")
                ->limit(5)
                ->get();
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $appData
     * @return int
     * @throws CustomException
     */
    public function status(array $appData): int
    {
        try {
            return $this->model
                ->query()
                ->where('chat_room_id', $appData['chat_room_id'])
                ->whereNot("user_id", $appData['user_id'])
                ->update(['status' => $appData['status']]);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $email
     * @return Builder|Model
     * @throws CustomException
     */
    public function getChatRoomId(string $email): Builder|Model
    {
        try {
            return ChatRoom::query()
                ->where('name', $email)
                ->firstOrFail();
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return Collection|array
     * @throws CustomException
     */
    public function messageList(): Collection|array
    {
        try {
            $limit = intval(Cache::get('record_limit', ChatService::LIMIT));
            $skip = intval(Cache::get('skip'));
            $response = $this->getMessages(ChatService::LIMIT, $skip);
            $number = $limit + ChatService::LIMIT;
            Cache::put('record_limit', $number);
            Cache::put('skip', $number);
            return $response;
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
