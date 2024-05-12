<?php

namespace App\Http\Resources;

use App\Gemini\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeminiChatResource extends JsonResource
{
    private $chat;

    public function __construct(ChatSession $chat)
    {
        $this->chat = $chat;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'chat_session' => $this->chat->getId(),
            'messages' => $this->formatMessages($this->chat->getParts()),
        ];
    }

    private function formatMessages(array $parts)
    {
        return collect($parts)->map(function ($part) {
            return ['role' => $part['role'], 'text' => $part['parts']['text']];
        });
    }
}
