<?php

namespace Admin\Http\Controllers;


use Admin\Http\Requests\StoreManagerProfileRequest;
use Admin\Http\Requests\UpdateManagerProfileRequest;
use Admin\Http\Resources\ManagerProfile\ManagerProfileResource;
use Admin\Services\ProfileService;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Auth;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;
use Throwable;

class ManagerProfileController extends Controller
{
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/me/profile",
     *      operationId="CreateManagerProfile",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Auth"},
     *      summary="Заполнение(создание) своего профиля менеджерами",
     *      @OA\RequestBody(
     *         required=true,
     *        @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *           required={"locate","name","surname","phone","city"},
     *           @OA\Property(property="locate",description="Язык(обязательно)", type="string",  example="ru"),
     *           @OA\Property(property="avatar", description="Аватар менеджера", type="string", example="/upload/avatar.png"),
     *           @OA\Property(property="name", description="Имя менеджера",type="string", example="Ivan"),
     *           @OA\Property(property="surname", description="Фамилия менеджера",type="string", example="Pupkin"),
     *           @OA\Property(property="phone",description="Телефон менеджера", type="string",  example="+79999999999"),
     *           @OA\Property(property="city", description="Город менеджера",type="string", example="Moscow"),
     *         )
     *       )
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
    public function store(StoreManagerProfileRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $profileData = $request->validated();
            return new ApiSuccessResponse(
                new  ManagerProfileResource($this->profileService->create($profileData)),
                ['message' => 'Профиль успешно создан'],
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
     *      path="/api/v1/admin/me/profile",
     *      operationId="ViewSelfManagerProfile",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Auth"},
     *      summary="Просмотр своего профиля менеджером",
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
    public function showSelf(): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->profileService->checkProfile(Auth::id())) {
            return new ApiSuccessResponse(
                new  ManagerProfileResource($this->profileService->showSelf()),
                ['message' => 'Успешно'],
                Response::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Профиль еще не создан',
            null,
            Response::HTTP_NOT_FOUND

        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/me/profile",
     *      operationId="UpdateManagerProfile",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Auth"},
     *      summary="Редактирование своего профиля менеджерами",
     *       @OA\RequestBody(
     *          required=true,
     *         @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            @OA\Property(property="locate",description="Язык(обязательно)", type="string",  example="ru"),
     *            @OA\Property(property="avatar", description="Аватар менеджера", type="string", example="/upload/avatar.png"),
     *            @OA\Property(property="name", description="Имя менеджера",type="string", example="Ivan"),
     *            @OA\Property(property="surname", description="Фамилия менеджера",type="string", example="Pupkin"),
     *            @OA\Property(property="phone",description="Телефон менеджера", type="string",  example="+79999999999"),
     *            @OA\Property(property="city", description="Город менеджера",type="string", example="Moscow"),
     *          )
     *        )
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
    public function update(UpdateManagerProfileRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $profileData = $request->validated();
//            dump($profileData);
            return new ApiSuccessResponse(
                new ManagerProfileResource($this->profileService->update($profileData)),
                ['message' => 'Успешно'],
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при редактировании профиля',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/managers/{id}/profile",
     *      operationId="ViewManagerProfile",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Пользователи"},
     *      summary="Просмотр профиля менеджера/куратора",
     *@OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
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
    public function show(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->profileService->checkProfile($id)) {
            return new ApiSuccessResponse(
                new  ManagerProfileResource($this->profileService->show($id)),
                ['message' => 'Успешно'],
                Response::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Профиль еще не создан',
            null,
            Response::HTTP_NOT_FOUND

        );
    }
}
