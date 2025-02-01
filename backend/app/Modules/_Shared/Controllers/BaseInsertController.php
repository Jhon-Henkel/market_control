<?php

namespace App\Modules\_Shared\Controllers;

use App\Modules\_Shared\Enum\Response\HttpStatusCodeEnum;
use App\Modules\_Shared\Utils\Validator\McValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseInsertController
{
    abstract public function __invoke(Request $request): JsonResponse;
    abstract protected function getInsertRules(): array;

    protected function response(array $data, HttpStatusCodeEnum $statusCode): JsonResponse
    {
        return response()->json($data, $statusCode->value);
    }

    protected function validateRequest(Request $request): array
    {
        McValidator::validateRequest($request, $this->getInsertRules());
        return $request->all();
    }
}
