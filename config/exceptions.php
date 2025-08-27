<?php

use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return [
    ModelNotFoundException::class => ResourceNotFoundException::class,
    ValidationException::class => ResourceValidationException::class,
    NotFoundHttpException::class => App\Exceptions\NotFoundHttpException::class,
    UnauthorizedException::class => App\Exceptions\UnauthorizedException::class,
    TokenExpiredException::class => App\Exceptions\TokenExpiredException::class,
];
