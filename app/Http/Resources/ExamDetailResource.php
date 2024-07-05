<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'time' => $this->time,
            'author' => new AuthorResource($this->author),
            'questions' => QuestionResource::collection($this->questions),
            'shuffle_questions' => $this->shuffle_questions,
            'shuffle_choices' => $this->shuffle_choices,
        ];
    }
}
