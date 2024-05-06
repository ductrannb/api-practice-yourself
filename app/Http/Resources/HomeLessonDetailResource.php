<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeLessonDetailResource extends JsonResource
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
            'course_id' => $this->course_id,
            'course_name' => $this->course->name ?? null,
            'questions' => QuestionResource::collection($this->questions),
            'total_questions' => $this->questions->count() ?? 0,
            'selected' => QuestionChoiceSelectedResource::collection($this->questionsSelected)
        ];
    }
}
