<?php

namespace App\Modules\Nfce\UseCase\GetDataFromNfceUrl;

use App\Modules\Nfce\Exceptions\EmptyProductDataException;
use App\Modules\Nfce\Exceptions\ProductsQuantityNotMatchWithPurchaseException;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GetDataFromNfceUrlUseCase
{
    public function execute(string $url): array
    {
        $fullHtml = $this->getHtmlFromUrl($url);
        $xpath = $this->getDomXpath($fullHtml);

        $products = $this->getProductsData($xpath);
        $purchaseData = $this->getPurchaseData($xpath);

        ProductsQuantityNotMatchWithPurchaseException::throwIfDiff(count($products), $purchaseData['total_items']);

        return [
            'products' => $products,
            'purchase_data' => $purchaseData,
            'fiscal_data' => $this->getFiscalData($xpath, $fullHtml)
        ];
    }

    protected function getHtmlFromUrl(string $url): string
    {
        $fullHtml = Http::get($url)->body();
        return Str::remove(["\n", "\r", "\t", "\u{A0}"], $fullHtml);
    }

    protected function getDomXpath(string $html): DOMXPath
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        return new DOMXPath($dom);
    }

    protected function getProductsData(DOMXPath $xpath): array
    {
        $productData = [];
        $rows = $xpath->query('//tr[starts-with(@id, "Item")]');

        foreach ($rows as $row) {
            $name = $xpath->query('.//span[@class="txtTit"]', $row)->item(0)->nodeValue;
            $quantity = $xpath->query('.//span[@class="Rqtd"]', $row)->item(0)->nodeValue;
            $unit = $xpath->query('.//span[@class="RUN"]', $row)->item(0)->nodeValue;
            $unitPrice = $xpath->query('.//span[@class="RvlUnit"]', $row)->item(0)->nodeValue;
            $totalPrice = $xpath->query('.//span[@class="valor"]', $row)->item(0)->nodeValue;

            $productData[] = [
                'name' => Str::trim(Str::upper($name)),
                'quantity' => floatval(Str::trim(Str::replace(",", ".", Str::remove("Qtde.:", $quantity)))),
                'unit' => Str::trim(Str::remove("UN: ", $unit)),
                'unit_price' => floatval(Str::replace(',', '.', Str::trim(Str::remove("Vl. Unit.:", $unitPrice)))),
                'total_price' => floatval(Str::replace(',', '.', Str::trim($totalPrice))),
            ];
        }
        EmptyProductDataException::throwIfEmpty($productData);
        return $productData;
    }

    protected function getPurchaseData(DOMXPath $xpath): array
    {
        $totalItems = $xpath->query('//div[@id="linhaTotal"]/span[@class="totalNumb"]')->item(0)->nodeValue;
        $subtotal = $xpath->query('//div[@id="linhaTotal"]/span[@class="totalNumb"]')->item(1)->nodeValue;
        $discount = $xpath->query('//div[@id="linhaTotal"]/span[@class="totalNumb"]')->item(2)?->nodeValue ?? 0;
        $amount = $xpath->query('//div[@id="linhaTotal" and @class="linhaShade"]/span[@class="totalNumb txtMax"]')->item(0)->nodeValue;

        return [
            'total_items' => intval($totalItems),
            'subtotal' => floatval(str_replace(',', '.', $subtotal)),
            'discount' => floatval(str_replace(',', '.', $discount)),
            'amount_to_pay' => floatval(str_replace(',', '.', $amount)),
        ];
    }

    protected function getFiscalData(DOMXPath $xpath, string $html): array
    {
        $infoNode = $xpath->query('//div[@id="infos"]/div/ul/li')->item(0)->nodeValue;

        return [
            'number' => Str::between($infoNode, 'NORMALNÃºmero: ', ' SÃ©rie'),
            'series' => Str::between($infoNode, 'SÃ©rie: ', ' EmissÃ'),
            'emission' => Str::between($infoNode, 'EmissÃ£o: ', '- Via Consumidor'),
            'key' => Str::trim(Str::remove(" ", Str::between($html, 'Chave de acesso:</strong><br><span class="chave">', '</span></li>')))
        ];
    }
}
