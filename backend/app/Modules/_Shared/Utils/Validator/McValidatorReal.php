<?php

namespace App\Modules\_Shared\Utils\Validator;

use App\Modules\_Shared\Utils\Validator\Exceptions\InvalidArrayDataException;
use App\Modules\_Shared\Utils\Validator\Exceptions\InvalidRequestDataException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class McValidatorReal
{
    public function validateRequest(Request $request, array $rules): void
    {
        $validate = Validator::make($request->all(), $rules, $this->rulesInsertMessages());
        if ($validate->fails()) {
            throw new InvalidRequestDataException($validate->errors());
        }
    }

    public function validateArrayData(array $array, array $rules): void
    {
        $validate = Validator::make($array, $rules, $this->rulesInsertMessages());
        if ($validate->fails()) {
            throw new InvalidArrayDataException($validate->errors());
        }
    }

    protected function rulesInsertMessages(): array
    {
        return array(
            'required' => 'O :attribute é obrigatório!',
            'unique' => 'O :attribute já existe!',
            'max' => 'O :attribute não pode ser maior que :max caracteres!',
            'min' => 'O :attribute não pode ser menor que :min caracteres!',
            'int' => 'O :attribute deve ser do tipo int!',
            'string' => 'O :attribute deve ser do tipo string!',
            'decimal' => 'O :attribute deve ser do tipo decimal com o mínimo de 0 casa e máximos de 2 casas!',
            'exists' => 'O :attribute não existe!',
        );
    }
}
