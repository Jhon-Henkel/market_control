<?php

namespace App\Modules\Nfce\Controller;

use App\Models\Nfce;
use App\Modules\_Shared\Controllers\BaseInsertController;
use App\Modules\_Shared\Enum\Response\HttpResponseLevelEnum;
use App\Modules\_Shared\Enum\Response\HttpStatusCodeEnum;
use App\Modules\_Shared\Response\ResponseError;
use App\Modules\Nfce\Exceptions\NfceNotProcessedException;
use App\Modules\Nfce\Exceptions\ProductsQuantityNotMatchWithPurchaseException;
use App\Modules\Nfce\Factory\NfceFactory;
use App\Modules\Nfce\UseCase\GetDataFromNfceUrl\GetDataFromNfceUrlUseCase;
use App\Modules\Nfce\UseCase\InsertNfce\InsertNfceUseCase;
use App\Modules\Nfce\UseCase\RevertNfce\RevertNfceUseCase;
use App\Modules\Nfce\UseCase\ValidateProcessedNfceUseCase\ValidateProcessedNfceUseCase;
use App\Modules\Product\Factory\ProductFactory;
use App\Modules\Product\UseCase\InsertProduct\InsertProductUseCase;
use App\Modules\Purchase\Factory\PurchaseFactory;
use App\Modules\Purchase\UseCase\InsertPurchase\InsertPurchaseUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NfceInsertController extends BaseInsertController
{
    public function __construct(
        protected GetDataFromNfceUrlUseCase $getDataFromNfceUrlUseCase,
        protected InsertNfceUseCase $insertNfceUseCase,
        protected InsertPurchaseUseCase $insertPurchaseUseCase,
        protected PurchaseFactory $purchaseFactory,
        protected NfceFactory $nfceFactory,
        protected ProductFactory $productFactory,
        protected InsertProductUseCase $insertProductUseCase,
        protected ValidateProcessedNfceUseCase $validateProcessedNfceUseCase,
        protected RevertNfceUseCase $revertNfceUseCase
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = $this->validateRequest($request);
            $dataArray = $this->getDataFromNfceUrlUseCase->execute($data['nfce_url']);

            $isValid = $this->validateProcessedNfceUseCase->execute($dataArray['fiscal_data']['key']);
            if ($isValid) {
                return $this->response(["Ok"], HttpStatusCodeEnum::HttpOk);
            }

            $nfceInputDTO = $this->nfceFactory->makeInputDtoByArray($dataArray['fiscal_data']);
            $nfceOutputDTO = $this->insertNfceUseCase->execute($nfceInputDTO);

            $purchaseInputDTO = $this->purchaseFactory->makeInputDtoByArray(
                $dataArray['purchase_data'],
                $nfceOutputDTO
            );
            $purchaseOutputDTO = $this->insertPurchaseUseCase->execute($purchaseInputDTO);

            $productsInputDTO = $this->productFactory->makeInputDtoByArray($dataArray['products']);
            $this->insertProductUseCase->execute($productsInputDTO, $purchaseOutputDTO);

            $nfce = Nfce::findOrFail($nfceOutputDTO->getId());
            $nfce->process();
            return $this->response(["Ok"], HttpStatusCodeEnum::HttpOk);
        } catch (ProductsQuantityNotMatchWithPurchaseException $e) {
            if (isset($e::$nfceId)) {
                $this->revertNfceUseCase->execute($e::$nfceId);
            }
            return ResponseError::responseError(
                "{$e->getMessage()}, revertendo operação. Tente novamente!",
                HttpStatusCodeEnum::HttpConflict,
                HttpResponseLevelEnum::Error
            );
        } catch (NfceNotProcessedException $e) {
            $this->revertNfceUseCase->execute($e::$nfceId);
            return ResponseError::responseError(
                "{$e->getMessage()}, revertendo operação. Tente novamente!",
                HttpStatusCodeEnum::HttpInternalServerError,
                HttpResponseLevelEnum::Error
            );
        }
    }

    protected function getInsertRules(): array
    {
        return ['nfce_url' => 'required|url'];
    }
}
