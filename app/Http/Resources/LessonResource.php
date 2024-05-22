<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            'author' => new AuthorResource($this->author),
            'count_question' => $this->questions->count() ?? 0,
            'completion' => $this->completion ?? 0,
            'course' => new CourseResource($this->course),
            'questions' => QuestionResource::collection($this->questions)
        ];
    }
}
