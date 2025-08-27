<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="OpenApi NEW ARTRUSSIA",
 *      description="api для новой платформы artrussia",
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server artrussia"
 * )
 * @OA\Tag(
 *     name="Admin",
 *     description="Админка"
 *     )
 * @OA\Tag(
 *     name="Lk",
 *     description="Личный кабинет участника"
 *     )
 * @OA\Tag(
 * name="Admin|Auth",
 * description="Админка (авторизация)"
 * ),
 * @OA\Tag(
 * name="Admin|Пользователи",
 * description="Пользователи (менеджеры/кураторы)"
 * ),
 *
 * @OA\Tag(
 * name="Admin|Участники",
 * description="Участники выставки"
 * ),
 * @OA\Tag(
 * name="Admin|Заявки",
 * description="Заявки на участие"
 * ),
 * @OA\Tag(
 *  name="Admin|Заявки|Комментарии менеджера",
 *  description="Комментарии к заявке после оценки"
 *  ),
 * @OA\Tag(
 *  name="Admin|Заявки|Оценка Комисии",
 *  description="Оценка заявки приемной комиссией"
 *  ),
 * @OA\Tag(
 * name="Admin|События",
 * description="Ежегодные выставки"
 * ),
 * @OA\Tag(
 * name="Admin|Спикеры",
 * description="Спикеры"
 * ),
 * @OA\Tag(
 * name="Admin|Команда проекта",
 * description="Команда проекта"
 * ),
 * @OA\Tag(
 *  name="Admin|Кураторы",
 *  description="Кураторы"
 *  ),
 * @OA\Tag(
 *   name="Admin|Художники",
 *   description="Участники"
 *   ),
 * @OA\Tag(
 *   name="Admin|Фотографы",
 *   description="Участники"
 *   ),
 * @OA\Tag(
 *   name="Admin|Скульпторы",
 *   description="Участники"
 *   ),
 * @OA\Tag(
 *    name="Admin|Галереи",
 *    description="Участники"
 *    ),
 * @OA\Tag(
 *       name="Admin|Категория партнёра",
 *       description="Категория партнёра"
 *       ),
 * @OA\Tag(
 *     name="Admin|Партнёры",
 *     description="Партнёры"
 *     ),
 * @OA\Tag(
 *     name="Admin|Программа",
 *     description="Программа"
 *     ),
 * @OA\Tag(
 * name="Admin|Роли-Доступы",
 * description="Роли и Доступы"
 * ),
 * @OA\Tag(
 * name="Admin|Каталог услуг",
 * description="Каталог услуг"
 * ),
 * @OA\Tag(
 * name="Admin|Категория товаров",
 * description="Категория товаров"
 * ),
 * @OA\Tag(
 * name="Admin|Товар",
 * description="Товар"
 * ),
 * @OA\Tag(
 *  name="Admin|Заказы",
 *  description="Заказы"
 *  ),
 * @OA\Tag(
 *  name="Admin|Слоты",
 *  description="Время выбора заезда /выезда"
 *  ),
 * @OA\Tag(
 *  name="Admin|VIP-гости",
 *  description="VIP-гости"
 *  ),
 * @OA\Tag(
 *  name="Admin|Рабочие",
 *  description="Рабочие"
 *  ),
 * @OA\Tag(
 *  name="Admin|Сотрудники",
 *  description="Сотрудники"
 *  ),
 * @OA\Tag(
 *   name="Admin|Мои документы",
 *   description="Мои документы, соглашения"
 *   ),
 * @OA\Tag(
 *   name="Admin|Схема стендов",
 *   description="Схема стендов"
 *   ),
 *
 * @OA\Tag(
 * name="Lk|Авторизация",
 * description="Личный кабинет участника (авторизация)"
 * )
 *  @OA\Tag(
 *    name="Lk|Схема стендов",
 *    description="Схема стендов"
 *    ),
 * @OA\Tag(
 * name="Lk|Заявки",
 * description="Заявки на участие"
 * ),
 * @OA\Tag(
 * name="Admin|Загрузка",
 * description="Загрузка"
 * ),
 *
 * @OA\Tag(
 * name="Lk|Заказы",
 * description="Заказы"
 * ),
 * @OA\Tag(
 *  name="Lk|Слоты",
 *  description="Время выбора заезда /выезда"
 *  ),
 * @OA\Tag(
 *  name="Lk|VIP-гости",
 *  description="VIP-гости"
 *  ),
 * @OA\Tag(
 *  name="Lk|Рабочие",
 *  description="Рабочие"
 *  ),
 * @OA\Tag(
 *    name="Lk|Сотрудники",
 *    description="Сотрудники"
 *    ),
 * @OA\Tag(
 *  name="Lk|Заявки|Комментарии менеджера",
 *  description="Комментарии к заявке"
 *  ),
 * @OA\Tag(
 *  name="Lk|Дополнительные Услуги",
 *  description="Дополнительные Услуги"
 *  ),
 * @OA\Tag(
 *  name="Lk|Оборудование в аренду",
 *  description="Оборудования в аренду"
 *  ),
 * @OA\Tag(
 * name="App|Locate",
 * description="Переключение языка"
 * ),
 * @OA\Tag(
 *  name="App|События",
 *  description="События"
 *  ),
 * @OA\Tag(
 *   name="App|Категория партнёра",
 *   description="Категория партнёра"
 *   ),
 * @OA\Tag(
 *    name="App|Партнёры",
 *    description="Партнёры"
 *    ),
 * @OA\Tag(
 *     name="App|Категория партнёра",
 *     description="Категория партнёра"
 *     ),
 * @OA\Tag(
 *   name="App|Команда проекта",
 *   description="Команда проекта"
 *  ),
 * @OA\Tag(
 *   name="App|Участники",
 *   description="Участники события"
 *   ),
 * @OA\Tag(
 *    name="App|Участники",
 *    description="Участник события"
 *    ),
 * @OA\Tag(
 *  name="App|Спикеры",
 *  description="Спикеры"
 *  ),
 * @OA\Tag(
 *  name="App|Команда проекта",
 *  description="Команда проекта"
 *  )
 * @OA\Tag(
 * name="Lk|Визуализация",
 * description="Визуализация"
 * ),
 * @OA\Tag(
 * name="Lk|Загрузка",
 * description="Загрузка"
 * ),
 * @OA\Tag(
 *  name="Lk|Профиль",
 *  description="Профиль"
 *  ),
 * @OA\Tag(
 *   name="Lk|Моя команда",
 *   description="Моя команда"
 *   ),
 * @OA\Tag(
 *      name="Admin|Classic",
 *      description="Админка|Арт Россия. Classic"
 *      ),
 * @OA\Get(
 *      path="/api/v1/admin/auth/get-permissions",
 *      tags={"Admin|Auth"},
 *      security={{"bearerAuth":{}}},
 *      summary="Для VueJs получение доступов авторизованного пользователя",
 *      operationId="Role&Permission",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/vnd.api+json",
 *
 *         )
 *     ),
 *     @OA\Response(
 *      response=200,
 *       description="Success",
 *      @OA\MediaType(
 *           mediaType="application/vnd.api+json",
 *      )
 *   ),
 * ),
 * @OA\Get(
 *      path="/api/v1/lk/auth/get-permissions",
 *      tags={"Lk|Авторизация"},
 *      security={{"bearerAuth":{}}},
 *      summary="Для VueJs получение доступов авторизованного пользователя",
 *      operationId="lk.Role&Permission",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/vnd.api+json",
 *         )
 *     ),
 * @OA\Response(
 *      response=200,
 *       description="Success",
 *      @OA\MediaType(
 *           mediaType="application/vnd.api+json",
 *      )
 *   ),
 *)
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
