<?php

namespace App\Http\Requests;

class GeminiSendMessageRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'chat_session' => 'required|string|exists:gemini_chats,id',
            'message' => 'required|string'
        ];
    }
}
