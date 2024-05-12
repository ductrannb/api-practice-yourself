<?php

namespace App\Gemini;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GeminiService
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

    public function sendMessage(string $message): array
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
                500,
                $response->json()['error']['message'] ?? 'Model error'
            );
        }
        if (!$candidate || !isset($candidate['content'])) {
            info('response in case 1: ', $response->json());
            return $this->responseWithCode(500, 'Time out or empty response');
        }
        $text = $candidate['content']['parts'][0]['text'] ?? null;
        if (!$text) {
            info('response in case 2: ', $response->json());
            return $this->responseWithCode(500, 'Time out or empty response');
        }
        $this->chat->addPart($text, Enum::ROLE_MODEL);
        $geminiChat = GeminiChat::find($this->chat->getId());
        $geminiChat->parts = $this->chat->getParts();
        $geminiChat->save();
        GeminiChatEvent::dispatch($this->chat);
        return $this->responseWithCode(200, $text);
    }

    private function responseWithCode($code, $message): array
    {
        return [
            'code' => $code,
            'message' => $message
        ];
    }
}
