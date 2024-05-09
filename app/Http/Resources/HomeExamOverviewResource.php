<?php

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeExamOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $perEasy = $this->getPercentage($this->questions->where('level', '=', Question::LEVEL_EASY)->count());
        $perMedium = $this->getPercentage($this->questions->where('level', '=', Question::LEVEL_MEDIUM)->count());
        $perHard = $this->getPercentage($this->questions->where('level', '=', Question::LEVEL_HARD)->count());
        return [
            'id' => $this->id,
            'name' => $this->name,
            'time' => $this->time,
            'count_question' => $this->questions->count() ?? 0,
            'percentage_easy' => $perEasy,
            'percentage_medium' => $perMedium,
            'percentage_hard' => $perHard,
            'histories' => HomeExamHistoryResource::collection($this->histories)
        ];
    }

    private function getPercentage($num)
    {
        if ($this->questions->count() == 0) {
            return 0;
        }
        return round(($num / $this->questions->count()) * 100 ?? 0, 2);
    }
}
