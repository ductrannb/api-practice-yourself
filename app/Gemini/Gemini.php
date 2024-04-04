<?php

namespace App\Gemini;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Gemini
{
    private const API_BASE_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:';
    public ChatSession $chat;

    public function __construct($chatId = null)
    {
        if ($chatId) {
            $this->continueChat($chatId);
        }
    }

    public function startChat($chatSession = null): ChatSession
    {
        GeminiChat::create(['id' => $chatSession, 'parts' => []]);
        $geminiChat = GeminiChat::latest('created_at')->first();
        $this->chat = new ChatSession($geminiChat->id);
        return $this->chat;
    }

    public function continueChat(string $chatSessionId): ChatSession
    {
        $chat = GeminiChat::find($chatSessionId);
        if (!$chat) {
            return $this->startChat($chatSessionId);
        }
        $this->chat = new ChatSession($chat->id, $chat->parts);
        return $this->chat;
    }

    /**
     * @param array $data: payloads
     * @param string $function: gemini function name
    **/
    private function request(string $function, array $data): Response
    {
        $url = self::API_BASE_URL . $function . '?key=' . config('services.gemini.key');
        return Http::post($url, $data);
    }

    public function generateContent(string $text)
    {

    }

    public function sendMessage(string $message): string|array
    {
        if (!$this->chat->getId()) {
            $this->startChat();
        }
        $this->chat->addPart($message);
        $response = $this->request('generateContent', [
            'contents' => $this->chat->getParts()
        ]);
        $candidate = $response->json()['candidates'][0] ?? null;
        if (isset($response->json()['error'])) {
            return $this->responseWithCode(
                $response->json()['error']['code'] ?? null,
                $response->json()['error']['message'] ?? 'Model error'
            );
        }
        if (!$candidate || !isset($candidate['content'])) {
            info('response in case 1: ', $response->json());
            return [500, 'Error in case 1'];
        }
        $text = $candidate['content']['parts'][0]['text'] ?? null;
        if (!$text) {
            info('response in case 2: ', $response->json());
            return [500, 'Error in case 2'];
        }
        $this->chat->addPart($text, Enum::ROLE_MODEL);
        $geminiChat = GeminiChat::find($this->chat->getId());
        $geminiChat->parts = $this->chat->getParts();
        $geminiChat->save();
        GeminiChatEvent::dispatch($this->chat);
        return [
            'code' => '200',
            'message' => $text
        ];
    }

    private function responseWithCode($code, $message): array
    {
        return [
            'code' => $code,
            'message' => $message
        ];
    }
}
