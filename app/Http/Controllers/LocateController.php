<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class LocateController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/changelang/{locate}",
     *      tags={"App|Locate"},
     *      summary="Смена языка ",
     *      operationId="ChangeLang",
     *      @OA\Parameter(
     *        name="locate",
     *        in="path",
     *        required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *
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
     */
    public function changeLocate(string $locate): string
    {
        app()->setLocale($locate);
        return app()->getLocale();
    }

    public function checkLocate(): string
    {
        return app()->getLocale();
    }
}
