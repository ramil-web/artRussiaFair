<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Role\StoreRequest;
use Admin\Http\Requests\Role\UpdateRequest;
use Admin\Http\Resources\Role\RoleCollection;
use Admin\Http\Resources\Role\RoleResource;
use Admin\Services\RoleService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;


class RoleController extends Controller
{
    private RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function store(StoreRequest $request): RoleResource
    {
        return new RoleResource($this->roleService->store($request));
    }
    /**
     * @OA\Patch(
     *      path="/api/v1/admin/roles/{id}",
     *      operationId="UpdateRoles",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Роли-Доступы"},
     *      summary="Редактирование роли",
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
     *          @OA\Property(property="desc", type="string", format="string", example="admin"),
     *          @OA\Property(property="permissions", type="integer", format="array", example="[1,2,3,5,9]"),
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
    public function update(UpdateRequest $request, int $id): RoleResource
    {
        return new RoleResource($this->roleService->update($id,$request->all()));
    }

    public function destroy(int $id): Response
    {
        $this->roleService->destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
    /**
     * @OA\Get(
     *      path="/api/v1/admin/roles",
     *      operationId="roles",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Роли-Доступы"},
     *      summary="Получить список ролей",
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
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
     * )
     **/
    public function index(Request $request): RoleCollection
    {
        return new RoleCollection($this->roleService->list($request));
    }
    /**
     * @OA\Get(
     *      path="/api/v1/admin/roles/{id}",
     *      operationId="rolesGet",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Роли-Доступы"},
     *      summary="Получить данные роли",
     *      @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
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
     * )
     **/
    public function show(int $id,Request $request): RoleResource
    {
        return new RoleResource($this->roleService->view($id,$request));
    }
}
