<?php

namespace App\Gemini;

class ChatSession
{
    private string $id;
    private array $parts;

    public function __construct(string $id, array $parts = [])
    {
        $this->id = $id;
        $this->parts = $parts;
    }

    public function addPart(string $message, string $role = Enum::ROLE_USER): void
    {
        $this->parts[] = [
            'role' => $role,
            'parts' => [
                'text' => $message
            ]
        ];
    }

    public function getParts(): array
    {
        return $this->parts;
    }

    public function setParts(array $parts): void
    {
        $this->parts = $parts;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
