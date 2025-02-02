<?php

use App\Modules\_Shared\Enum\Response\HttpResponseLevelEnum;
use App\Modules\_Shared\Enum\Response\HttpStatusCodeEnum;
use App\Modules\_Shared\Exceptions\HttpExceptions\BadRequestException;
use App\Modules\_Shared\Response\ResponseError;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (BadRequestException $e) {
            return ResponseError::responseError(
                $e->getMessage(),
                HttpStatusCodeEnum::HttpBadRequest,
                HttpResponseLevelEnum::Validation
            );
        });

        $exceptions->render(function (Throwable $e) {
            return ResponseError::responseError(
                $e->getMessage(),
                HttpStatusCodeEnum::HttpInternalServerError,
                HttpResponseLevelEnum::Error
            );
        });
    })->create();
