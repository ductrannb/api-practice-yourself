<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeExamReviewResource extends JsonResource
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
            'name' => $this->exam->name ?? null,
            'exam_id' => $this->exam_id,
            'total_question' => $this->exam->questions->count() ?? 0,
            'score' => $this->score,
            'questions' => QuestionResource::collection($this->exam->questions),
            'selected' => QuestionChoiceSelectedResource::collection($this->selected),
        ];
    }
}
