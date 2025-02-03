<?php

namespace App\Modules\ChatBot\UseCase\NfceProcess;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\ChatBot\Enum\ResponseChatEnum;
use App\Modules\Nfce\UseCase\InsertByChatbot\InsertByChatbotUseCase;
use DOMDocument;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NfceProcessUseCase
{
    public function __construct(private readonly InsertByChatbotUseCase $insertByChatbotUseCase)
    {
    }

    public function execute(array $data, string $chatId, string $cacheKey, string $url): ResponseChatEnum
    {
        if (isset($data['message']['photo'])) {
            Log::info('Foto recebida, processando QR Code...');
            $photo = end($data['message']['photo']);
            $fileId = $photo['file_id'];

            $response = Http::get(sprintf("https://api.telegram.org/bot%s/getFile?file_id=%s", config('app.telegram_token'), $fileId));
            $filePath = $response->json()['result']['file_path'];

            $imageUrl = sprintf("https://api.telegram.org/file/bot%s/%s", config('app.telegram_token'), $filePath);
            $imageContent = file_get_contents($imageUrl);

            if (is_dir('/tmp') === false) {
                mkdir('/tmp', 777, true);
            }

            $imagePath = '/tmp/qr_code_image.jpg';
            file_put_contents($imagePath, $imageContent);

            $url = $this->processQRCode($imagePath);

            if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                ResponseChat::interactWithUser($chatId, "QR Code da NFC-e lido: " . $url);
            } else {
                Log::error('QR Code inválido');
                ResponseChat::interactWithUser($chatId, "O QR Code não contém uma URL válida de NFC-e.");
                cache()->forget($cacheKey);
                return ResponseChatEnum::InvalidUrl;
            }
        } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
            Log::error('Link nfce inválido');
            ResponseChat::interactWithUser($chatId, "O link enviado não parece ser válido. Por favor, envie um link correto.");
            cache()->forget($cacheKey);
            return ResponseChatEnum::InvalidUrl;
        }

        $result = $this->insertByChatbotUseCase->execute($url);

        if ($result['status'] === 'error') {
            Log::error('Erro ao processar nfce');
            ResponseChat::interactWithUser($chatId, "Ocorreu um erro ao processar a NFC-e. Por favor, tente novamente.");
            return ResponseChatEnum::ErrorToProcessNfce;
        }

        Log::info('NFC-e processada com sucesso');
        ResponseChat::interactWithUser($chatId, "NFC-e processada com sucesso!");
        cache()->forget($cacheKey);
        return ResponseChatEnum::Ok;
    }

    protected function processQRCode(string $imagePath): ?string
    {
        $url = 'https://zxing.org/w/decode';
        $response = Http::attach('f', file_get_contents($imagePath), 'qr_code_image.jpg')->post($url);
        $data = $response->body();

        Log::info('QR Code Lido');

        $dom = new DOMDocument();
        @$dom->loadHTML($data);
        $preTags = $dom->getElementsByTagName('pre');

        if ($preTags->length > 0) {
            Log::info('QR Code Processado');
            return trim($preTags->item(0)->nodeValue);
        }

        Log::error('QR Code não contém uma URL válida.');
        return null;
    }
}
