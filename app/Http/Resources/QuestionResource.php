<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'content' => $this->content,
            'level' => $this->level,
            'choices' => QuestionChoiceResource::collection($this->choices),
            'correct_choice' => new QuestionChoiceResource($this->correctChoices->first()) ?? null,
            'solution' => $this->solution ?: '',
            'author' => new AuthorResource($this->author),
            'assignable_id' => $this->assignable_id,
            'assignable_type' => $this->assignable_type,
            'is_selected' => $this->is_selected ?? false
        ];
    }
}
