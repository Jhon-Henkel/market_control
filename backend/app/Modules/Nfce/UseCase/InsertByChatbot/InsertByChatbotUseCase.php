<?php

namespace App\Modules\Nfce\UseCase\InsertByChatbot;

use App\Models\Nfce;
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

class InsertByChatbotUseCase
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

    public function execute(string $nfceUrl): array
    {
        try {
            $dataArray = $this->getDataFromNfceUrlUseCase->execute($nfceUrl);

            $isValid = $this->validateProcessedNfceUseCase->execute($dataArray['fiscal_data']['key']);
            if ($isValid) {
                return ['status' => 'already_processed'];
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

            return ['status' => 'ok'];
        } catch (ProductsQuantityNotMatchWithPurchaseException $e) {
            if (isset($e::$nfceId)) {
                $this->revertNfceUseCase->execute($e::$nfceId);
            }
            return ['status' => 'error', 'message' => "{$e->getMessage()}, revertendo operação. Tente novamente!"];
        } catch (NfceNotProcessedException $e) {
            $this->revertNfceUseCase->execute($e::$nfceId);
            return ['status' => 'error', 'message' => "{$e->getMessage()}, revertendo operação. Tente novamente!"];
        }
    }
}
