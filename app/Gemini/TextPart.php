<?php

namespace App\Gemini;

class TextPart
{
    private string $text;
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function toArray()
    {
        return [
            'text' => $this->text
        ];
    }
}
