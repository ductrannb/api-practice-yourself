<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeExamHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->exam->name ?? null,
            'score' => $this->score,
            'count_correct_question' => $this->count_correct_question,
            'total_question' => $this->total_question,
            'created_at' => $this->created_at
        ];
    }
}
