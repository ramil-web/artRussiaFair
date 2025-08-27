<?php

namespace Admin\Repositories\Chat;

use Admin\Repositories\BaseRepository;
use Admin\Services\ChatService;
use App\Exceptions\CustomException;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use App\Services\StorageService;
use Auth;
use Cache;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Respect\Validation\Exceptions\FileException;
use Symfony\Component\HttpFoundation\Response;

class ChatRepository extends BaseRepository
{
    const CHAT_FILES = 'chat_files';

    public function __construct(
        ChatMessage              $model,
        protected StorageService $storageService,
        protected User           $user
    ) {
        parent::__construct($model);
    }

    /**
     * @param array $dataApp
     * @return Collection|array
     * @throws CustomException
     */
    public function chatParticipants(array $dataApp): Collection|array
    {
        try {
            $currentUser = Auth::user()->email;
            $sortBy = array_key_exists('sort_by', $dataApp) ? 'users.' . $dataApp['sort_by'] : 'users.id';
            $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'asc';
            $query = User::query()
                ->whereNot('email', $currentUser)
                ->leftJoin('chat_rooms as cr', 'cr.name', 'users.email')
                ->select([
                    'users.id as user_id', 'cr.id as chat_room_id', 'users.email as email', 'users.username'
                ])
                ->orderBy($sortBy, $orderBy);

            if (array_key_exists('email', $dataApp)) {
                $email = $dataApp['email'];
                $query = $query->where("email", "LIKE", "%{$email}%");
            }
            if (array_key_exists('user_id', $dataApp)) {
                $userId = $dataApp['user_id'];
                $query = $query->where("user_id", "LIKE", "%{$userId}%");
            }

            $response = $query->get();
            $messages = $this->getUnreadMessages();
            foreach ($response as $key => &$val) {
                if (isset($messages[$val['chat_room_id']])) {
                    $val->unreadedMessages = $messages[$val['chat_room_id']];
                } else {
                    $val->unreadedMessages = 0;
                }
            }
            return $response;
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @param array $appData
     * @return Collection|array
     * @throws CustomException
     */
    public function messages(array $appData): Collection|array
    {
        try {
            if (array_key_exists('chat_room_id', $appData)) {
                return $this->getChatRoom($appData['chat_room_id']);
            } else {
                $chatRoomId = $this->checkChatRoomId($appData['user_id']);
                $user = User::query()->findOrFail($appData['user_id']);
                return [
                    'id'           => null,
                    'user_id'      => $appData['user_id'],
                    'chat_room_id' => $chatRoomId,
                    'user'         => $user
                ];
            }
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param int $chatRoomId
     * @param int $limit
     * @param int $skip
     * @return Collection|array
     * @throws CustomException
     */
    private function getChatroom(int $chatRoomId, int $limit = ChatService::LIMIT, $skip = 0): Collection|array
    {
        try {
            return $this->model
                ->query()
                ->where([
                    'chat_room_id' => $chatRoomId
                ])
                ->with(['user'])
                ->orderBy('created_at', 'ASC')
                ->skip($skip)
                ->take($limit)
                ->get();
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @throws CustomException
     */
    public function createMessage(array $appData)
    {
        try {
            $user = Auth::user();
            if (array_key_exists('file', $appData) && !empty($appData['file'])) {
                $filePath = $this->upload($appData);
            }

            $chatRoom = $this->checkChatRoomId($appData['user_id']);

            $data = [
                'user_id'      => $user->id,
                'chat_room_id' => $chatRoom->id,
                'message'      => $appData['message'] ?? null,
                'file_name'    => $appData['file_name'] ?? null,
                'file_path'    => $filePath ?? null
            ];
            $message = $this->create($data);
            $message->recipient_email = $chatRoom->name;
            return $message;
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return HigherOrderBuilderProxy|mixed
     * @throws CustomException
     */
    private function checkChatRoomId(int $userId): mixed
    {
        try {
            $userEmail = User::query()->findOrFail($userId)->email;
            $chatRoom = ChatRoom::query()
                ->where('name', $userEmail);
            if ($chatRoom->exists()) {
                return $chatRoom->firstOrFail();
            } else {
                return ChatRoom::query()
                    ->create(['name' => $userEmail]);
            }
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $appData
     * @return string
     * @throws CustomException
     */
    private function upload(array $appData): string
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
    public function deleteData(int $id): mixed
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
     * @throws CustomException
     */
    private function messageRead(array $appData): Collection|array
    {
        try {
            $userId = Auth::user()->id;
            return $this->model
                ->query()
                ->where('chat_room_id', $appData['chat_room_id'])
                ->whereNot('user_id', $userId)
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
     * @return array
     * @throws CustomException
     */
    private function getUnreadMessages(): array
    {
        try {
            return $this->model->query()
                ->select('chat_room_id', DB::raw('COUNT(*) as total'))
                ->where('status', false)
                ->groupBy('chat_room_id')
                ->pluck('total', 'chat_room_id')->toArray();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $chatRoomId
     * @return Collection|array
     * @throws CustomException
     */
    public function messageList(int $chatRoomId): Collection|array
    {
        try {
            $limit = intval(Cache::get('admin_record_limit', ChatService::LIMIT));
            $skip = intval(Cache::get('admin_skip'));
            $response = $this->getChatroom($chatRoomId, ChatService::LIMIT, $skip);
            $number = $limit + ChatService::LIMIT;
            Cache::put('admin_record_limit', $number);
            Cache::put('admin_skip', $number);
            return $response;
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
