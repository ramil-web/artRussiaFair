<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\SchemaOfStand\SchemaOfStandRequest;
use Admin\Http\Requests\SchemaOfStand\SchemaOfStandStoreRequest;
use Admin\Http\Resources\SchemaOfStand\SchemaOfStandResource;
use Admin\Services\SchemaOfStandService;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SchemaOfStandController extends Controller
{
    public function __construct(public SchemaOfStandService $service)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/schema-of-stand/store",
     *      operationId="AdminShemaUpload",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Схема стендов"},
     *      summary="Добавление Схему для стендов",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             required={"name", "url"},
     *             @OA\Property(property="url", type="string", example="http://storage/app/uploads/some_doc.pdf"),
     *             @OA\Property(property="name", type="string", example="some_doc.pdf"),
     *             ),
     *           ),
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="data", type="array",
     *                   @OA\Items(
     *                       @OA\Property(property="id", type="integer", example=1),
     *                       @OA\Property(property="name", type="string", example="some_doc.pdf"),
     *                       @OA\Property(property="url", type="string", example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app/schema-of-stand/some_doc.pdf"),
     *                       @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                       @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 ),
     *               ),
     *           ),
     *        )
     *      ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404,description="not found"),
     *            @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *               @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *               ),
     *            ),
     *          ),
     *       ),
     *      @OA\Response(response=500,description="Server error")
     * )
     * @param SchemaOfStandStoreRequest $request
     * @return JsonResponse
     * @throws CustomException
     */
    public function store(SchemaOfStandStoreRequest $request): JsonResponse
    {
        $dataApp = $request->validated();
        $documents = $this->service->store($dataApp);
        return response()->json([
            'data' => [
                $documents
            ],
        ]);
    }

    /**
     * @OA\Delete(
     *    path="/api/v1/admin/schema-of-stand/delete",
     *    operationId="AdminShemaDelete",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Схема стендов"},
     *    summary="Удаление Схемы для стендов",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       description="ID схемы для стеднов",
     *       @OA\Schema(
     *           type="integer",
     *           example="1",
     *        )
     *     ),
     *    @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *       mediaType="application/vnd.api+json",
     *       @OA\Schema(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Файл успешно удален."),
     *           ),
     *         ),
     *       ),
     *    ),
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
     * @param SchemaOfStandRequest $request
     * @return JsonResponse
     * @throws CustomException
     */
    public function delete(SchemaOfStandRequest $request): JsonResponse
    {
        $appData = $request->validated();
        $documents = $this->service->delete($appData['id']);
        return response()->json([
            'data' => [
                'status'  => $documents,
                'message' => 'Файл успешно удален.'
            ],
        ]);
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/schema-of-stand/show",
     *    operationId="AdminShemaShow",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Схема стендов"},
     *    summary="Получаем Схему для стендов",
     *    @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="schema", type="string", example="schema"),
     *                  @OA\Property(
     *                     property="attributes",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="some_doc.pdf"),
     *                     @OA\Property(property="url", type="string", example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app/schema-of-stand/some_doc.pdf"),
     *                     @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                     @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  ),
     *                  @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/admin/schema/1")
     *                  ),
     *                    ),
     *                  @OA\Property(property="metadata", type="object",
     *                  @OA\Property(property="message", type="string", example="Ok"),
     *                 ),
     *              ),
     *           ),
     *        ),
     *    @OA\Response(response=401, description="Unauthenticated"),
     *    @OA\Response(response=400,description="Bad Request"),
     *    @OA\Response(response=404, description="Not Found"),
     *    @OA\Response(response=403,description="Forbidden",
     *       @OA\JsonContent(
     *           @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                 @OA\Property(property="status", example="403"),
     *                 @OA\Property(property="detail", example="User does not have the right roles.")
     *              ),
     *           ),
     *         ),
     *      ),
     * )
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new  SchemaOfStandResource($this->service->show()),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }
}
