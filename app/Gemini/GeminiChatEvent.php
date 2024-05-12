<?php

namespace App\Gemini;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GeminiChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatSession $chatSession;

    public function __construct(ChatSession $chatSession)
    {
        $this->chatSession = $chatSession;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('gemini.' . $this->chatSession->getId());
    }

    public function broadcastAs()
    {
        return 'gemini-chat';
    }
}
