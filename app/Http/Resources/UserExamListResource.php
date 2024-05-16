<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserExamListResource extends JsonResource
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
            'count_question' => $this->questions->count(),
            'submit_time' => $this->histories->count() ?? 0,
            'best_score' => $this->formatScore($this->histories->max('score')),
            'worst_score' => $this->formatScore($this->histories->min('score')),
            'avg_score' => $this->formatScore($this->histories->avg('score'))
        ];
    }

    private function formatScore($score): float
    {
        return round($score, 2) ?? 0;
    }
}
