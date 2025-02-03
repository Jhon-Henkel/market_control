<?php

namespace App\Modules\_Shared\Response;

use App\Modules\_Shared\Enum\Response\HttpResponseLevelEnum;
use App\Modules\_Shared\Enum\Response\HttpStatusCodeEnum;
use Illuminate\Http\JsonResponse;

class ResponseError
{
    public function responseError(string $message, HttpStatusCodeEnum $statusCode, HttpResponseLevelEnum $level): JsonResponse
    {
        return response()->json(['message' => $message, 'status' => $statusCode->value, 'level' => $level->value], $statusCode->value);
    }
}
