<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Exceptions\JsonApiException;
use Throwable;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{

//    use HandlesErrors;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [

    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        JWTException::class,
    ];



    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $e): Response|JsonResponse
    {
        if (!$request->wantsJson()) {
            return parent::render($request, $e);
        }

        $exception = $this->handleJsonApiException($e);

        return response()
            ->json($exception->makeResponseData(), $exception->getCode())
            ->header('Content-Type', 'application/vnd.api+json');
    }

    public function handleJsonApiException(Throwable $e): JsonApiException
    {
        if ($e instanceof JsonApiException) {
            return $e;
        }

        $exceptionsClass = config(sprintf('exceptions.%s', get_class($e))) ?? 'App\Exceptions\ResponseErrorException';

        return new $exceptionsClass($e);
    }
}
