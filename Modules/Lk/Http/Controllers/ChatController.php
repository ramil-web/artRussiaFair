<?php

namespace Lk\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\User;
use Auth;
use Lk\Events\MessageSent;
use Lk\Http\Requests\Chat\ChatMessageShowRequest;
use Lk\Http\Requests\Chat\ChatMessageStatusRequest;
use Lk\Http\Requests\Chat\ChatMessageUpdateRequest;
use Lk\Http\Requests\Chat\NewMessageRequest;
use Lk\Http\Requests\Chat\SearchMessageRequest;
use Lk\Http\Resources\Chat\ChatResource;
use Lk\Services\ChatService;
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
     *    path="/api/v1/lk/chat/message/all",
     *    tags={"Lk|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Переписка ползователя",
     *    operationId="Lk.GetMessages",
     *    @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID ползователя",
     *        @OA\Schema(
     *           type="integer",
     *           example="1"
     *        )
     *    ),
     *    @OA\Response(
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
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="user_id", type="integer", example="1"),
     *                 @OA\Property(property="chat_room_id", type="integer", example="1"),
     *                 @OA\Property(property="message", type="string", example="Hi"),
     *                 @OA\Property(property="file_path", type="string", example=""),
     *                 @OA\Property(property="file_name", type="string", example=""),
     *                 @OA\Property(property="status", type="bool", example="false"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example="1"),
     *                     @OA\Property(property="username", type="string", example="admin"),
     *                     @OA\Property(property="email", type="string", example="admin@synergy.ru"),
     *                     @OA\Property(property="email_verified_at", type="string",example="2023-11-27 15:1"),
     *                     @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                     @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                     @OA\Property(property="deleted_at", type="string", example=null),
     *                 ),
     *                  ),
     *               ),
     *             @OA\Property(property="metadata",type="object",
     *                  @OA\Property(property="message", example="Ok"),
     *               ),
     *            ),
     *         ),
     *      ),
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
    public function messages(): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            return new ApiSuccessResponse(
                $this->chatService->getMessage(),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'An error occurred while trying to create the user',
                $exception
            );
        }
    }

    /**
     * @OA\Post(
     *    path="/api/v1/lk/chat/message/store",
     *    tags={"Lk|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Отправка сообщений или файла",
     *    operationId="lkCreateMessage",
     *    @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *              @OA\Property(property="file", type="string",example=""),
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
            broadcast(new MessageSent($userModel, $message))->toOthers();
            return new ApiSuccessResponse(
                $message,
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'The message has not been sent',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/chat/message/show",
     *    tags={"Lk|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Просмотр одной сообщении",
     *    operationId="lkShowMessage",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
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
     *      path="/api/v1/lk/chat/message/update",
     *      tags={"Lk|Чат"},
     *      security={{"bearerAuth":{}}},
     *      summary="Обновление сообщение",
     *      operationId="lkUpdateMessage",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                 @OA\Property(property="id", type="integer",example=1),
     *                  @OA\Property(property="chat_room_id", type="integer",example=1),
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
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'The message has not been updated',
                $exception
            );
        }
    }

    /**
     * @OA\Delete (
     *    path="/api/v1/lk/chat/message/delete",
     *    tags={"Lk|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Удаление одной сообщении",
     *    operationId="lkDeleteMessage",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
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
     *    path="/api/v1/lk/chat/manager/show",
     *    tags={"Lk|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Просмотр профил менеджера",
     *    operationId="lkShowMnagerProfile",
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
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function manager(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new ChatResource($this->chatService->manager()),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/chat/message/search",
     *    tags={"Lk|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Поиск по чату",
     *    operationId="lkChatSearch",
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
     *    path="/api/v1/lk/chat/message/status",
     *    tags={"Lk|Чат"},
     *    security={{"bearerAuth":{}}},
     *    summary="Отчет об читаемости",
     *    operationId="LkMessageReaded",
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
     *        description="ID участника",
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
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'The status of the message has not been updated.',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/chat/message/list",
     *    tags={"Lk|Чат|Сообщения с лимитом"},
     *    security={{"bearerAuth":{}}},
     *    summary="Переписка ползователя",
     *    operationId="Lk.GetMessagesList",
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
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="user_id", type="integer", example="1"),
     *                  @OA\Property(property="chat_room_id", type="integer", example="1"),
     *                  @OA\Property(property="message", type="string", example="Hi"),
     *                  @OA\Property(property="file_path", type="string", example=""),
     *                  @OA\Property(property="file_name", type="string", example=""),
     *                  @OA\Property(property="status", type="bool", example="false"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="user", type="object",
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="username", type="string", example="admin"),
     *                      @OA\Property(property="email", type="string", example="admin@synergy.ru"),
     *                      @OA\Property(property="email_verified_at", type="string",example="2023-11-27 15:1"),
     *                      @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                      @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                      @OA\Property(property="deleted_at", type="string", example=null),
     *                  ),
     *                   ),
     *                ),
     *              @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Ok"),
     *                ),
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
     * /**
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function messagesByLimit(): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            return new ApiSuccessResponse(
                $this->chatService->getMessageList(),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'An error occurred while trying to create the user',
                $exception
            );
        }
    }
}
