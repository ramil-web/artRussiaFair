<?php

namespace Lk\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Lk\Http\Resources\SchemaOfStand\SchemaOfStandResource;
use Lk\Services\SchemaOfStandService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SchemaOfStandController extends Controller
{
    public function __construct(public SchemaOfStandService $service)
    {
    }
    /**
     * @OA\Get(
     *    path="/api/v1/lk/schema-of-stand/show",
     *    operationId="LkShemaShow",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Схема стендов"},
     *    summary="Получаем Схему для стендов",
     *    @OA\Response(
     *           response=200,
     *           description="Success",
     *           @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="schema", type="string", example="schema"),
     *                 @OA\Property(
     *                    property="attributes",
     *                    type="object",
     *                    @OA\Property(property="id", type="integer", example=1),
     *                    @OA\Property(property="name", type="string", example="some_doc.pdf"),
     *                    @OA\Property(property="url", type="string", example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app/schema-of-stand/some_doc.pdf"),
     *                    @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                    @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 ),
     *                 @OA\Property(
     *                    property="links",
     *                    type="object",
     *                    @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/schema/1")
     *                 ),
     *                   ),
     *                 @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="message", type="string", example="Ok"),
     *                ),
     *             ),
     *          ),
     *       ),
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
