<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Manager\ListRequest;
use Admin\Http\Requests\Manager\ManagerRequest;
use Admin\Http\Requests\Manager\UpdateManagerRequest;
use Admin\Http\Resources\Managers\ManagerCollection;
use Admin\Http\Resources\Managers\ManagerResource;
use Admin\Repositories\Manager\ManagerRepository;
use Admin\Services\ManagerService;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Jobs\Chat\SendMessageToMailJob;
use App\Models\Role;
use App\Models\User;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ManagerController extends Controller
{

    public function __construct(
        public ManagerService    $managerService,
        public ManagerRepository $managerRepository,
    )
    {
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/me",
     *      tags={"Admin|Auth"},
     *      security={{"bearerAuth":{}}},
     *      summary="Мои данные (авторизованного пользователя)",
     *      operationId="AdminMe",
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *     ),
     * @OA\Response(
     *       response=200,
     *       description="Success",
     *              @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     */
    public function me(): ApiSuccessResponse
    {
        $user = User::with('roles', 'managerProfile')->find(auth()->id());
        return new ApiSuccessResponse(
            new ManagerResource($user),
            ['message' => 'OK'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/me",
     *      operationId="UpdateSelfManager",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Auth"},
     *      summary="Редактирование менеджером/куратором своих данных",
     *    @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          @OA\Property(property="username", description="Юзернейм",type="string", example="LordAdmin"),
     *          @OA\Property(property="email", type="string", format="email", example="admin@mail.ru"),
     *        )
     *      )
     *     ),
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/

    public function updateSelf(UpdateManagerRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $userData = $request->validated();
            $user = $this->managerService->update(auth()->id(), $userData);
            return new ApiSuccessResponse(
                new ManagerResource($user->with('roles')),
                ['message' => 'Успешно'],
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
     * @OA\Get(
     *      path="/api/v1/admin/managers",
     *      operationId="ManagerAll",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Пользователи"},
     *      summary="Получить список всех менеджеров и кураторов",
     *      description="Получаем список всех доступных менеджеров и кураторов",
     *     @OA\Parameter(
     *         name="filter[roles]",
     *         in="query",
     *         description="Фильтр по ролям доступные для менеджеров manager,commission,super_admin",
     *
     *         @OA\Schema(
     *               type="string",
     *               enum={"manager","commission","super_admin"},
     *             )
     *     ),
     *      @OA\Parameter(
     *         name="filter[trashed]",
     *         in="query",
     *         description="показать удаленных(архивных) (with/only)",
     *      @OA\Schema(
     *               type="string",
     *               enum={"with","only"},
     *             )
     *          ),
     *     @OA\Parameter(
     *         name="filter[username]",
     *         in="query",
     *         description="фильтр (поиск) по username",
     *      @OA\Schema(
     *               type="string",
     *             )
     *          ),
     *     @OA\Parameter(
     *         name="filter[email]",
     *         in="query",
     *         description="фильтр(поиск) по email",
     *      @OA\Schema(
     *               type="string",
     *
     *             )
     *          ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="пагинация по умолчанию 10",
     *      @OA\Schema(
     *               type="integer",
     *                          )
     *          ),
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/
    public function index(ListRequest $request): ManagerCollection
    {
        return new ManagerCollection($this->managerService->list($request));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/managers/{id}",
     *      operationId="GetManager",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Пользователи"},
     *      summary="Получить данные менеджера/куратора",
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *           type="integer"
     *        ),
     *     ),
     *   ),
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/
    public function show(int $id, Request $request): ApiErrorResponse|ApiSuccessResponse
    {
        if (!$this->managerService->view($id, $request)) {
            return new ApiErrorResponse(
                'Такого пользователя нет или он находится в архиве',
                null,
                ResponseAlias::HTTP_NOT_FOUND
            );
        }
        return new ApiSuccessResponse(
            new ManagerResource($this->managerService->view($id, $request)),
            ['message' => 'OK'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/manager/store",
     *     operationId="CreateManager",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Пользователи"},
     *     summary="Создать нового менеджера/куратора",
     *     @OA\Parameter(
     *          name="role",
     *          in="query",
     *          description="Роль, менеджер/куратор",
     *          required=true,
     *          @OA\Schema(
     *             type="string",
     *             enum={"manager","commission"},
     *             example="commission",
     *          )
     *       ),
     *     @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          @OA\Property(property="email", type="string", format="email", example="pupukin@mail.ru"),
     *        )
     *      )
     *     ),
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/
    public function store(ManagerRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $Data = $request->validated();

            $Data['username'] = Str::before($Data['email'], '@');
            $Data['password'] = bin2hex(random_bytes(10 / 2));
            $role = Arr::pull($Data, 'role');

            $user = $this->managerService->create($Data);
            $roles = Role::findByName($role, 'api');
            $user->assignRole($roles);


            SendMessageToMailJob::dispatch(
                'Новое сообщение от Art Russia',
                'emails.manager-registration-mail',
                [
                    'login'    => $user->username,
                    'password' => $Data['password'],
                    'url'      => env('ADMIN_LINK'),
                    'role'     => $role == "commission" ? "Куратор" : "Менеджер",
                    'email'    => $Data['email'],
                    'prefix'   => 'admin'
                ]
            )->delay(now()->addSeconds(3));

            return new ApiSuccessResponse(
                new ManagerResource(User::with('roles')->find($user->id)),
                ['message' => 'Менеджер успешно создан'],
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
     * @OA\Patch(
     *      path="/api/v1/admin/managers/{id}",
     *      operationId="UpdateManager",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Пользователи"},
     *      summary="Редактирование менеджера/куратора",
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *    @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          @OA\Property(property="username", type="string", format="string", example="admin"),
     *          @OA\Property(property="email", type="string", format="email", example="admin@mail.ru"),
     *          @OA\Property(property="role",description="Роль менеджера",type="string",enum={"manager","commission","super_Admin"},example="commission"),
     *
     *        )
     *      )
     *     ),
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/

    public function update(int $userId, UpdateManagerRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $userData = $request->validated();
            $role = Arr::pull($userData, 'role');


            $user = $this->managerService->update($userId, $userData);
            $roles = Role::findByName($role, 'api');
            $user->syncRoles($roles);
            return new ApiSuccessResponse(
                new ManagerResource($user),
                ['message' => 'Успешно'],
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
     * @OA\Delete(
     *      path="/api/v1/admin/managers/{id}/archive",
     *      operationId="ArchiveManager",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Пользователи"},
     *      summary="Добавить менеджера/куратора в архив",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *          type="integer"
     *           )
     * ),
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/
    public function softDelete(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->checkUser($id)) {
            $this->managerService->softDelete($id);
            return new ApiSuccessResponse(
                null,
                ['message' => 'Успешно добавлен в архив'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Такого пользователя не существует',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }

    public function checkUser(int $id): bool
    {
        if (User::withTrashed()->find($id)) {
            return true;
        }
        return false;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/managers/{id}/restore",
     *      operationId="RestoreManager",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Пользователи"},
     *      summary="Восстановить менеджера/куратора из архива",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *           type="integer"
     *           )
     * ),
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/
    public function restore(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->checkUser($id)) {
            return new ApiSuccessResponse(
                new ManagerResource($this->managerService->restore($id)),
                ['message' => 'успешно восстановлен'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Такого пользователя не существует',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/managers/{id}",
     *      operationId="DeleteManager",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Пользователи"},
     *      summary="Полностью удалить менеджера/куратора и данные",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *     type="integer"
     *           )
     * ),
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/
    public function destroy(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->checkUser($id)) {
            $this->managerService->forceDelete($id);
            return new ApiSuccessResponse(
                null,
                ['message' => 'Пользователь успешно полностью удален'],
                ResponseAlias::HTTP_NO_CONTENT
            );
        }
        return new ApiErrorResponse(
            'Такого пользователя не существует',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }
}
