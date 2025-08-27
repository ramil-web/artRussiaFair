<?php

namespace Admin\Http\Controllers;

use Admin\Events\AdminChatEvent;
use Admin\Http\Requests\Chat\ChatMessagesByLimitRequest;
use Admin\Http\Requests\Chat\ChatMessageShowRequest;
use Admin\Http\Requests\Chat\ChatMessageStatusRequest;
use Admin\Http\Requests\Chat\ChatMessageUpdateRequest;
use Admin\Http\Requests\Chat\ChatParticipantsRequest;
use Admin\Http\Requests\Chat\ListChatMessagesRequest;
use Admin\Http\Requests\Chat\NewMessageRequest;
use Admin\Http\Requests\Chat\SearchMessageRequest;
use Admin\Http\Resources\Chat\ChatResource;
use Admin\Services\ChatService;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\User;
use Auth;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ChatController extends Controller
{
    public function __construct(protected ChatService $chatService)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/chat/participants",
     *    tags={"Admin|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Список участников",
     *    operationId="AdminChatParticipants",
    @OA\Parameter(
     *           name="user_id",
     *           in="query",
     *           description="Филтьр, ID участника",
     *           @OA\Schema(
     *              type="integer",
     *          )
     *       ),
     * @OA\Parameter(
     *          name="email",
     *          in="query",
     *          description="Фильтр, почта участника",
     *          @OA\Schema(
     *             type="string",
     *          )
     *       ),
     * @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Сортировка по поле",
     *          @OA\Schema(
     *             type="string",
     *             enum={"id","email","created_at", "updated_at"},
     *           )
     *       ),
     * @OA\Parameter(
     *          name="order_by",
     *          in="query",
     *          description="Порядок сортировки",
     *          @OA\Schema(
     *             type="string",
     *             enum={"ASC", "DESC"},
     *           )
     *        ),
     * @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *          type="object",
     *          @OA\Property(
     *             property="data",
     *             type="array",
     *             @OA\Items(
     *             @OA\Property(property="user_id", type="integer", example="1"),
     *             @OA\Property(property="chat_room_id", type="integer", example="1"),
     *             @OA\Property(property="email", type="string", example="test@cmail.com"),
     *             @OA\Property(property="username", type="string", example="participant"),
     *             ),
     *              ),
     *            ),
     *         ),
     *      ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=400,description="Bad Request"),
     * @OA\Response(response=404, description="Not Found"),
     * @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                  @OA\Property(property="status", example="403"),
     *                  @OA\Property(property="detail", example="User does not have the right roles.")
     *              ),
     *           ),
     *         ),
     *      ),
     * )
     */
    public function chatParticipants(ChatParticipantsRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->chatService->chatParticipants($appData),
                ['message' => 'Ok'],
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'An error occurred while trying to receive chat participants',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/chat/messages",
     *    tags={"Admin|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Переписка c ползователем",
     *    operationId="AdminChatMessages",
     *    @OA\Parameter(
     *        name="chat_room_id",
     *        in="query",
     *        required=false,
     *        description="ID комнаты (переписки)",
     *        @OA\Schema(
     *            type="integer",
     *            example="1"
     *         ),
     *     ),
     *    @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         description="ID участника",
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *          ),
     *      ),
     *    @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *              property="data",
     *              type="array",
     *              @OA\Items(
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="user_id", type="integer", example="1"),
     *              @OA\Property(property="chat_room_id", type="integer", example="1"),
     *              @OA\Property(property="message", type="string", example="Привет"),
     *              @OA\Property(property="file_path", type="string", example=null),
     *              @OA\Property(property="file_name", type="string", example=null),
     *              @OA\Property(property="status", type="string", example=false),
     *              @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *              @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *              ),
     *               ),
     *             ),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                  @OA\Property(property="status", example="403"),
     *                  @OA\Property(property="detail", example="User does not have the right roles.")
     *              ),
     *           ),
     *         ),
     *      ),
     * )
     */
    public function messages(ListChatMessagesRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->chatService->getMessages($appData),
                ['message' => 'Ok'],
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'An error occurred while trying to to receive chat messages',
                $exception
            );
        }
    }

    /**
     * @OA\Post(
     *    path="/api/v1/admin/chat/store",
     *    tags={"Admin|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Отправка сообщений или файла",
     *    operationId="AdminCreateMessage",
     *    @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *              @OA\Property(property="file", type="string",example=""),
     *              @OA\Property(property="user_id", type="integer",example=1),
     *              @OA\Property(property="file_name", type="stringr",example="image.jpg"),
     *              @OA\Property(property="message", type="stringr",example="привет"),
     *             ),
     *          ),
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *              @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *    @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     * @param NewMessageRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function newMessage(NewMessageRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $user = Auth::user();
            $userModel = User::query()->find($user->id);
            $appData = $request->validated();
            $message = $this->chatService->createMessage($appData);
            broadcast(new AdminChatEvent($userModel, $message, $message->recipient_email))->toOthers();
            return new ApiSuccessResponse(
                $message,
                ['message' => 'Ok'],
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'An error occurred while trying to create the user',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/chat/show",
     *    tags={"Admin|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Просмотр одной сообщении",
     *    operationId="AdminShowMessage",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       description="ID сообщения",
     *       @OA\Schema(
     *          type="integer",
     *          example="1"
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *        description="Success",
     *               @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *       )
     *    ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=400,description="Bad Request"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                 @OA\Property(property="status", example="403"),
     *                 @OA\Property(property="detail", example="User does not have the right roles.")
     *             ),
     *          ),
     *        ),
     *     ),
     * )
     * @param ChatMessageShowRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(ChatMessageShowRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new ChatResource($this->chatService->show($appData['id'])),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/chat/update",
     *      tags={"Admin|Чат"},
     *      security={{"bearerAuth":{}}},
     *      summary="Обновление сообщение",
     *      operationId="AdminUpdateMessage",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                 @OA\Property(property="id", type="integer",example=1),
     *                 @OA\Property(property="chat_room_id", type="integer",example=1),
     *                 @OA\Property(property="message", type="stringr",example="привет"),
     *             ),
     *          ),
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *              @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *    @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     * @param ChatMessageUpdateRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(ChatMessageUpdateRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->chatService->update($appData),
                ['message' => 'The message has been successfully updated'],
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'An error occurred while trying to update message',
                $exception
            );
        }
    }

    /**
     * @OA\Delete (
     *    path="/api/v1/admin/chat/delete",
     *    tags={"Admin|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Удаление одной сообщении",
     *    operationId="AdminDeleteMessage",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       description="ID сообщения",
     *       @OA\Schema(
     *          type="integer",
     *          example="1"
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *        description="Success",
     *               @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *       )
     *    ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=400,description="Bad Request"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                 @OA\Property(property="status", example="403"),
     *                 @OA\Property(property="detail", example="User does not have the right roles.")
     *             ),
     *          ),
     *        ),
     *     ),
     * )
     * @param ChatMessageShowRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function delete(ChatMessageShowRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            $this->chatService->delete($appData['id']),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/chat/search",
     *    tags={"Admin|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Поиск по чату",
     *    operationId="AdminChatSearch",
     *    @OA\Parameter(
     *        name="chat_room_id",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *           type="integer",
     *           example="1"
     *        )
     *    ),
     *    @OA\Parameter(
     *       name="message",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *          type="string",
     *          example="при"
     *        )
     *     ),
     *    @OA\Response(
     *       response=200,
     *        description="Success",
     *               @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *       )
     *    ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=400,description="Bad Request"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                 @OA\Property(property="status", example="403"),
     *                 @OA\Property(property="detail", example="User does not have the right roles.")
     *             ),
     *          ),
     *        ),
     *     ),
     * )
     * @param SearchMessageRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function search(SearchMessageRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            $this->chatService->searchMessage($appData),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/admin/chat/status",
     *    tags={"Admin|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Отчет об читаемости",
     *    operationId="AdminMessageReaded",
     *    @OA\Parameter(
     *         name="chat_room_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *            type="integer",
     *            example="1"
     *         )
     *     ),
     *    @OA\Parameter(
     *       name="status",
     *       in="query",
     *       description="Отчет об читаемости",
     *       required=true,
     *       @OA\Schema(
     *          type="string",
     *          enum={"true"}
     *         )
     *      ),
     *    @OA\Parameter(
     *        name="user_id",
     *        in="query",
     *        required=true,
     *        description="ID менеджера",
     *        @OA\Schema(
     *           type="integer",
     *           example="1"
     *        )
     *    ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *              @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *    @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     * @param ChatMessageStatusRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function status(ChatMessageStatusRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->chatService->status($appData),
                ['message' => 'Ok'],
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'An error occurred while trying to create the user',
                $exception
            );
        }
    }

     /**
      * @OA\Get(
      *    path="/api/v1/admin/chat/messages/by-limit",
      *    tags={"Admin|Чат"},
      *    security={{"bearerAuth":{}}},
      *    summary="Получает переписку по лимиту",
      *    operationId="AdminChatMessagesByLimit",
      *    @OA\Parameter(
      *        name="chat_room_id",
      *        in="query",
      *        required=false,
      *        description="ID комнаты (переписки)",
      *        @OA\Schema(
      *            type="integer",
      *            example="1"
      *         ),
      *     ),
      *    @OA\Response(
      *        response=200,
      *        description="Success",
      *        @OA\MediaType(
      *           mediaType="application/vnd.api+json",
      *           @OA\Schema(
      *           type="object",
      *           @OA\Property(
      *              property="data",
      *              type="array",
      *              @OA\Items(
      *              @OA\Property(property="id", type="integer", example="1"),
      *              @OA\Property(property="user_id", type="integer", example="1"),
      *              @OA\Property(property="chat_room_id", type="integer", example="1"),
      *              @OA\Property(property="message", type="string", example="Привет"),
      *              @OA\Property(property="file_path", type="string", example=null),
      *              @OA\Property(property="file_name", type="string", example=null),
      *              @OA\Property(property="status", type="string", example=false),
      *              @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
      *              @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
      *              ),
      *               ),
      *             ),
      *          ),
      *       ),
      *      @OA\Response(response=401, description="Unauthenticated"),
      *      @OA\Response(response=400,description="Bad Request"),
      *      @OA\Response(response=404, description="Not Found"),
      *      @OA\Response(response=403,description="Forbidden",
      *          @OA\JsonContent(
      *              @OA\Property(property="errors", type="array",
      *              @OA\Items(
      *                  @OA\Property(property="status", example="403"),
      *                  @OA\Property(property="detail", example="User does not have the right roles.")
      *              ),
      *           ),
      *         ),
      *      ),
      * )
      * */
    public function messagesByLimit(ChatMessagesByLimitRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->chatService->getMessageList($appData['chat_room_id']),
                ['message' => 'Ok'],
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'An error occurred while trying to create the user',
                $exception
            );
        }
    }
}
