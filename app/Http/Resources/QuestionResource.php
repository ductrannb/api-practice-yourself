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
            'is_selected' => $this->is_selected ?? false,
            'class' => $this->unit->parent->parent->name ?? null,
            'class_id' => $this->unit->parent->parent->id ?? null,
            'chapter_id' => $this->unit->parent->id ?? null,
            'chapter' => $this->unit->parent->name ?? null,
            'unit_id' => $this->unit->id ?? null,
            'unit' => $this->unit->name ?? null,
            'usage_count' => $this->questionMappings->count() ?? 0,
            'learning_module_id' => $this->learning_module_id,
        ];
    }
}
