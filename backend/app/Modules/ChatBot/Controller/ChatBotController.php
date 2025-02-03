<?php

namespace App\Modules\ChatBot\Controller;

use App\Modules\Nfce\UseCase\InsertByChatbot\InsertByChatbotUseCase;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class ChatBotController
{
    public function __construct(private InsertByChatbotUseCase $insertByChatbotUseCase)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        Log::info('Iniciando Conversa');

        $data = $request->all();

        if (! isset($data['message'])) {
            return response()->json(['status' => 'no message']);
        }

        $chatId = $data['message']['chat']['id'];
        $message = strtolower($data['message']['text'] ?? '');
        $username = $data['message']['from']['username'] ?? 'Sem username';

        Log::info("Chat ID: {$chatId} - Mensagem: {$message} - Username: {$username}");

        if (!in_array($username, config('app.telegram_allowed_usernames'))) {
            Log::error("Usuário não autorizado: {$username}");
            return response()->json(['status' => 'unauthorized']);
        }

        $cacheKey = "telegram_{$chatId}_step";
        $step = cache($cacheKey, 'default');

        if ($message === '/start') {
            Log::info('/start');
            $this->interactWithUser($chatId, "Olá, bem-vindo ao Chatbot do Market Control. Para começar, use um dos comandos disponíveis: \n\n/nfce -> Processar NFC-e\n/end -> Finalizar conversa");
            return response()->json(['status' => 'ok']);
        }

        if ($message === '/end') {
            Log::info('Conversa finalizada');
            $this->interactWithUser($chatId, "Até mais!");
            cache()->forget($cacheKey);
            return response()->json(['status' => 'ok']);
        }

        if ($message === '/nfce') {
            Log::info('/nfce');
            $this->interactWithUser($chatId, "Por favor, envie o link ou a foto do QR-Code da NFC-e.");
            cache([$cacheKey => 'waiting_nfce'], now()->addMinutes(5));
        } elseif ($step === 'waiting_nfce') {
            if (isset($data['message']['photo'])) {
                Log::info('Foto recebida, processando QR Code...');
                $photo = end($data['message']['photo']);
                $fileId = $photo['file_id'];

                $url = sprintf("https://api.telegram.org/bot%s/getFile?file_id=%s", config('app.telegram_token'), $fileId);
                $response = Http::get($url);
                $filePath = $response->json()['result']['file_path'];

                $imageUrl = sprintf("https://api.telegram.org/file/bot%s/%s", config('app.telegram_token'), $filePath);
                $imageContent = file_get_contents($imageUrl);

                if (is_dir('/tmp') === false) {
                    mkdir('/tmp', 777, true);
                }

                $imagePath = '/tmp/qr_code_image.jpg';
                file_put_contents($imagePath, $imageContent);

                $qrCodeData = $this->processQRCode($imagePath);

                if ($qrCodeData && filter_var($qrCodeData, FILTER_VALIDATE_URL)) {
                    $this->interactWithUser($chatId, "QR Code da NFC-e lido: " . $qrCodeData);

                    $result = $this->insertByChatbotUseCase->execute($qrCodeData);

                } else {
                    Log::error('QR Code inválido');
                    $this->interactWithUser($chatId, "O QR Code não contém uma URL válida de NFC-e.");
                    cache()->forget($cacheKey);
                    return response()->json(['status' => 'invalid url']);
                }
            } elseif (!filter_var($message, FILTER_VALIDATE_URL)) {
                Log::error('Link nfce inválido');
                $this->interactWithUser($chatId, "O link enviado não parece ser válido. Por favor, envie um link correto.");
                cache()->forget($cacheKey);
                return response()->json(['status' => 'invalid url']);
            }

            if (! isset($result)) {
                $result = $this->insertByChatbotUseCase->execute($message);
            }

            if ($result['status'] === 'error') {
                Log::error('Erro ao processar nfce');
                $this->interactWithUser($chatId, "Ocorreu um erro ao processar a NFC-e. Por favor, tente novamente.");
                return response()->json(['status' => 'error to process nfce']);
            }

            Log::info('NFC-e processada com sucesso');
            $this->interactWithUser($chatId, "NFC-e processada com sucesso!");
            cache()->forget($cacheKey);
        }

        Log::info('Conversa finalizada');
        return response()->json(['status' => 'ok']);
    }

    protected function interactWithUser(int|string $chatId, string $message): void
    {
        $urlSendMessage = sprintf("https://api.telegram.org/bot%s/sendMessage", config('app.telegram_token'));
        Http::post($urlSendMessage, ['chat_id' => $chatId, 'text' => $message]);
    }

    protected function processQRCode(string $imagePath): ?string
    {
//        try{
//            $result = new QRCode()->readFromFile($imagePath);
//            return (string)$result;
//        }
//        catch(Throwable $e){
//            Log::error('Erro ao processar QR Code: ' . $e->getMessage());
//            return null;
//        }
        $url = 'https://zxing.org/w/decode';

        $response = Http::attach(
            'f', file_get_contents($imagePath), 'qr_code_image.jpg'
        )->post($url);

        $data = $response->json();

        Log::info('QR Code Data: ' . json_encode($data));
        if (isset($data[0]['symbol'][0]['data'])) {
            return $data[0]['symbol'][0]['data'];
        }

        return null;
    }
}
