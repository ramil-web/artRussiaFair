<?php

namespace Lk\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\Response;
use Lk\Http\Requests\StoreUserProfileRequest;
use Lk\Http\Requests\UpdateUserProfileRequest;
use Lk\Http\Resources\UserProfile\UserProfileResource;
use Lk\Services\ProfileService;
use OpenApi\Annotations as OA;
use Throwable;

class UserProfileController extends Controller
{


    public function __construct(public ProfileService $profileService)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/me/profile",
     *      operationId="CreateUserProfile",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Профиль"},
     *      summary="Заполнение(создание) своего профиля пользователем",
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          required={"locate","name","surname","phone","city"},
     *          @OA\Property(property="locate",description="Язык(обязательно)", type="string",  example="ru"),
     *          @OA\Property(property="avatar", description="Аватар", type="string", example="/upload/avatar.png"),
     *          @OA\Property(property="name", description="Имя",type="string", example="Ivan"),
     *          @OA\Property(property="surname", description="Фамилия",type="string", example="Pupkin"),
     *          @OA\Property(property="phone",description="Телефон", type="string",  example="+79999999999"),
     *          @OA\Property(property="city", description="Город",type="string", example="Moscow"),
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
    public function store(StoreUserProfileRequest $request): ApiSuccessResponse|ApiErrorResponse
    {

        try {
            $profileData = $request->validated();
            return new ApiSuccessResponse(
                new  UserProfileResource($this->profileService->create($profileData)),
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
     *      path="/api/v1/lk/me/profile",
     *      operationId="ViewSelfUserProfile",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Профиль"},
     *      summary="Просмотр своего профиля пользователем",
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
    public function show(): ApiSuccessResponse
    {
        $UserProfile = $this->profileService->show(auth()->id());

        return new ApiSuccessResponse(
            new  UserProfileResource($UserProfile),
            ['message' => 'Ok'],
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/lk/me/profile",
     *      operationId="LkUpdateUserProfile",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Профиль"},
     *      summary="Редактирование своего профиля пользователем",
     *    @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          required={"name","surname","phone","city"},
     *           @OA\Property(property="locate",description="Язык(обязательно)", type="string",  example="ru"),
     *           @OA\Property(property="avatar", description="Аватар", type="string", example="/upload/avatar.png"),
     *           @OA\Property(property="name", description="Имя",type="string", example="Ivan"),
     *           @OA\Property(property="surname", description="Фамилия",type="string", example="Pupkin"),
     *           @OA\Property(property="phone",description="Телефон", type="string",  example="+79999999999"),
     *           @OA\Property(property="city", description="Город",type="string", example="Moscow"),
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
    public function update(UpdateUserProfileRequest $request): ApiSuccessResponse|ApiErrorResponse
    {

        try {
            $profileData = $request->all();
            return new ApiSuccessResponse(
                new UserProfileResource($this->profileService->update($profileData)),
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


}
