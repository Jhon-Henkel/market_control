<?php

use App\Modules\_Shared\Enum\Response\HttpStatusCodeEnum;
use App\Modules\_Shared\Exceptions\HttpExceptions\BadRequestException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (BadRequestException $e) {
            $code = HttpStatusCodeEnum::HttpBadRequest->value;
            $message = json_decode($e->getMessage());
            return response()->json(['message' => $message, 'status' => $code, 'level' => 'validation'], $code);
        });

        $exceptions->render(function (Throwable $e) {
            $code = HttpStatusCodeEnum::HttpInternalServerError->value;
            $message = $e->getMessage();
            return response()->json(['message' => $message, 'status' => $code, 'level' => 'error'], $code);
        });
    })->create();
