<?php

namespace Lk\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Auth;
use Lk\Http\Requests\VipGuests\DeleteVipGuestRequest;
use Lk\Http\Requests\VipGuests\ListVipGuestRequest;
use Lk\Http\Requests\VipGuests\StoreVipGuestRequest;
use Lk\Http\Requests\VipGuests\UpdateVipGuestRequest;
use Lk\Http\Resources\VipGuest\VipGuestCollection;
use Lk\Http\Resources\VipGuest\VipGuestResource;
use Lk\Repositories\UserApplication\UserApplicationRepository;
use Lk\Services\VipGuestService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class VipGuestController extends Controller
{
    public function __construct(
        public VipGuestService           $vipGuestService,
        public UserApplicationRepository $userApplicationRepository,
    )
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/vip-guest/store",
     *      operationId="Lk.createVipGuest",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|VIP-гости"},
     *      summary="Добавление VIP-гостя",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"full_name","user_application_id","organization","email"},
     *                  @OA\Property(property="full_name",description="Полное имя", type="string",  example="John Doe"),
     *                  @OA\Property(
     *                      property="user_application_id",description="ID заявки пользователя",type="integer",example=1
     *                  ),
     *                  @OA\Property(property="organization",description="Организация",type="string",example="VIP Company"),
     *                  @OA\Property(property="email",description="Email", type="string",example="john.doe@example.com"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                property="data",
     *                type="object",
     *                @OA\Property(property="id", type="integer", example="1"),
     *                @OA\Property(property="vip-guest", type="string", example="vip-guest"),
     *                @OA\Property(
     *                   property="attributes",
     *                   type="object",
     *                   @OA\Property(property="id", type="integer", example="1"),
     *                   @OA\Property(property="user_application_id", type="integer", example="1"),
     *                   @OA\Property(property="full_name", type="object",
     *                       @OA\Property(property="ru", type="string", example="John Doe"),
     *                   ),
     *                   @OA\Property(property="organization", type="object",
     *                      @OA\Property(property="ru", type="string", example="VIP Company")
     *                   ),
     *                   @OA\Property(property="email", type="sting", example="test.doe@example.com"),
     *                   @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                   @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                   @OA\Property(property="user_applications", type="object"),
     *                ),
     *                @OA\Property(
     *                   property="links",
     *                   type="object",
     *                   @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/vip-guest/1")
     *                ),
     *                @OA\Property(property="relationships",type="object",
     *                   @OA\Property(property="user_applications",type="object"),
     *                   ),
     *                  ),
     *                @OA\Property(property="metadata", type="object",
     *                @OA\Property(property="message", type="string", example="VIP-гость успешно добавлен"),
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
     * @param StoreVipGuestRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function store(StoreVipGuestRequest $request): ApiErrorResponse|ApiSuccessResponse
    {
        try {
            $dataApp = $request->validated();
            $vipGuest = $this->vipGuestService->create($dataApp);
            return new ApiSuccessResponse(
                new VipGuestResource($vipGuest),
                ['message' => 'VIP-гость успешно добавлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при добавлении VIP-гостя', $e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/vip-guest/all",
     *      tags={"Lk|VIP-гости"},
     *      security={{"bearerAuth":{}}},
     *      summary="Список всех VIP-гостей",
     *      operationId="Lk.getVipGuests",
     *      @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="Фильтр по id",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *     @OA\Parameter(
     *           name="filter[user_application_id]",
     *           in="query",
     *           description="Фильтр по ID Заявки",
     *           @OA\Schema(
     *               type="string",
     *           )
     *       ),
     *       @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                property="data",
     *                type="array",
     *                @OA\Items(
     *                @OA\Property(property="id", type="integer", example="1"),
     *                @OA\Property(property="vip-guest", type="string", example="vip-guest"),
     *                @OA\Property(
     *                   property="attributes",
     *                   type="object",
     *                   @OA\Property(property="id", type="integer", example="1"),
     *                   @OA\Property(property="user_application_id", type="integer", example="1"),
     *                   @OA\Property(property="full_name", type="object",
     *                       @OA\Property(property="ru", type="string", example="John Doe"),
     *                   ),
     *                   @OA\Property(property="organization", type="object",
     *                      @OA\Property(property="ru", type="string", example="VIP Company")
     *                   ),
     *                   @OA\Property(property="email", type="sting", example="test.doe@example.com"),
     *                   @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                   @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                ),
     *                @OA\Property(
     *                   property="links",
     *                   type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/vip-guest/1")
     *                ),
     *                @OA\Property(property="relationships",type="object",
     *                   @OA\Property(property="user_applications",type="object"),
     *                   ),
     *                  ),
     *                ),
     *                @OA\Property(property="links", type="object",
     *                @OA\Property(property="self", type="string",example="http://newapiartrussiafair/api/v1/lk/vip-guest/all"),
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
     * @param ListVipGuestRequest $request
     * @return VipGuestCollection|ApiErrorResponse
     * @throws CustomException
     */
    public function list(ListVipGuestRequest $request): VipGuestCollection|ApiErrorResponse
    {
        $user = Auth::user();
        $dataApp = $request->validated();
        return new VipGuestCollection($this->vipGuestService->list($user->id, $dataApp));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/vip-guest/{id}",
     *      operationId="Lk.getVipGuest",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|VIP-гости"},
     *      summary="Просмотр VIP-гостя",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *            @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *               property="data",
     *               type="object",
     *               @OA\Property(property="id", type="integer", example="1"),
     *               @OA\Property(property="vip-guest", type="string", example="vip-guest"),
     *               @OA\Property(
     *                  property="attributes",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="user_application_id", type="integer", example="1"),
     *                  @OA\Property(property="full_name", type="object",
     *                      @OA\Property(property="ru", type="string", example="John Doe"),
     *                  ),
     *                  @OA\Property(property="organization", type="object",
     *                     @OA\Property(property="ru", type="string", example="VIP Company")
     *                  ),
     *                  @OA\Property(property="email", type="sting", example="test.doe@example.com"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="user_applications", type="object"),
     *               ),
     *               @OA\Property(
     *                  property="links",
     *                  type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/vip-guest/1")
     *               ),
     *               @OA\Property(property="relationships",type="object",
     *                  @OA\Property(property="user_applications",type="object"),
     *                  ),
     *                 ),
     *               @OA\Property(property="metadata", type="object",
     *               @OA\Property(property="message", type="string", example="Ok"),
     *              ),
     *           ),
     *        ),
     *     ),
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
     * @param int $id
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(int $id): ApiSuccessResponse
    {
        $user = Auth::user();
        return new ApiSuccessResponse(
            new VipGuestResource($this->vipGuestService->show($id, $user->id)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/lk/vip-guest/update/{id}",
     *      operationId="LkUpdateVipGuest",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|VIP-гости"},
     *      summary="Редактирование VIP-гостя",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *            type="integer",
     *            example="1",
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"full_name","user_application_id","organization","email"},
     *                  @OA\Property(property="full_name",description="Полное имя", type="string",  example="John Doe"),
     *                  @OA\Property(property="user_application_id",description="ID заявки пользователя",type="integer",example=1),
     *                  @OA\Property(property="organization",description="Организация",type="string",example="VIP Company"),
     *                  @OA\Property(property="email",description="Email",type="string", example="john.doe@example.com"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                property="data",
     *                type="object",
     *                @OA\Property(property="id", type="integer", example="1"),
     *                @OA\Property(property="vip-guest", type="string", example="vip-guest"),
     *                @OA\Property(
     *                   property="attributes",
     *                   type="object",
     *                   @OA\Property(property="id", type="integer", example="1"),
     *                   @OA\Property(property="user_application_id", type="integer", example="1"),
     *                   @OA\Property(property="full_name", type="object",
     *                       @OA\Property(property="ru", type="string", example="John Doe"),
     *                   ),
     *                   @OA\Property(property="organization", type="object",
     *                      @OA\Property(property="ru", type="string", example="VIP Company")
     *                   ),
     *                   @OA\Property(property="email", type="sting", example="test.doe@example.com"),
     *                   @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                   @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                   @OA\Property(property="user_applications", type="object"),
     *                ),
     *                @OA\Property(
     *                   property="links",
     *                   type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/vip-guest/1")
     *                ),
     *                @OA\Property(property="relationships",type="object",
     *                   @OA\Property(property="user_applications",type="object"),
     *                   ),
     *                  ),
     *                @OA\Property(property="metadata", type="object",
     *                @OA\Property(property="message", type="string", example="VIP-гость успешно добавлен"),
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
     * @param int $id
     * @param UpdateVipGuestRequest $request
     * @return ApiSuccessResponse|ResponseAlias
     * @throws CustomException
     */
    public function update(int $id, UpdateVipGuestRequest $request): ApiSuccessResponse|ResponseAlias
    {
        $user = Auth::user();
        $dataApp = $request->validated();
        try {
            $response = $this->vipGuestService->update($id, $user->id, $dataApp);
            return new ApiSuccessResponse(
                new VipGuestResource($response),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (CustomException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Delete(
     *    path="/api/v1/lk/vip-guest/delete",
     *    operationId="LkDeleteVipGuest",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|VIP-гости"},
     *    summary="Удаление VIP-гостя",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *          type="integer",
     *          example="1",
     *         )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *               @OA\Property(property="data", type="boolean", example="true"),
     *               @OA\Property(property="metadata",type="object",
     *                  @OA\Property(property="message", example="Ok"),
     *               ),
     *             ),
     *          ),
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
     * @param DeleteVipGuestRequest $request
     * @return ApiSuccessResponse|ResponseAlias
     * @throws CustomException
     */
    public function delete(DeleteVipGuestRequest $request): ApiSuccessResponse|ResponseAlias
    {
        $dataApp = $request->validated();
        try {
            $response = $this->vipGuestService->delete($dataApp['id']);
            return new ApiSuccessResponse(
                $response,
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (CustomException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
