<?php

namespace App\Http\Controllers;

use App\Gemini\ChatSession;
use App\Gemini\GeminiService;
use App\Http\Requests\GeminiSendMessageRequest;
use App\Http\Resources\GeminiChatResource;
use Illuminate\Http\Request;

class GeminiChatController extends Controller
{
    private static string $apiKey;
    private ChatSession $chat;

    public function __construct()
    {
        self::$apiKey = config('services.gemini.key');
    }

    public function index($uuid = null)
    {
        $gemini = new GeminiService($uuid);
        $this->chat = $uuid ? $gemini->continueChat($uuid) : $gemini->startChat();
        return $this->responseOk(data: new GeminiChatResource($this->chat));
    }

    public function sendMessage(GeminiSendMessageRequest $request)
    {
        $gemini = new GeminiService($request->chat_session);
        if (!$request->chat_session) {
            $chat = $gemini->startChat();
        } else {
            $chat = $gemini->chat;
        }
        $result = $gemini->sendMessage($request->message);

        return $this->responseOk(data: $result);
    }
}
