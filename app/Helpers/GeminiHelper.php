<?php

namespace App\Helpers;

use App\Gemini\ChatSession;
use App\Gemini\GeminiService;

class GeminiHelper
{
    private static string $apiKey;
    private ChatSession $chat;
    public function __construct($chatSession = null)
    {
        $gemini = new GeminiService($chatSession);
        $this->chat = $gemini->chat;
        self::$apiKey = config('services.gemini.key');
    }

    public function textGenerate()
    {
        $request = request();
        $gemini = new GeminiService($request->chat_session);
        if (!$request->chat_session) {
            $chat = $gemini->startChat();
        } else {
            $chat = $gemini->chat;
        }
        $result = $gemini->sendMessage($request->message);

        return response()->json([
            'chat_session' => $chat->getId(),
            'data' => $result
        ]);
    }
}
