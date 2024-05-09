<?php

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeCourseDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalQuestion = $this->questions->count() ?? 0;
        $perEasy = $this->getPercentage($this->questions->where('level', '=', Question::LEVEL_EASY)->count(), $totalQuestion);
        $perMedium = $this->getPercentage($this->questions->where('level', '=', Question::LEVEL_MEDIUM)->count(), $totalQuestion);
        $perHard = $this->getPercentage($this->questions->where('level', '=', Question::LEVEL_HARD)->count(), $totalQuestion);
        $isBought = $this->users->where('id', '=', auth()->id())->count() > 0 ?? false;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lessons' => LessonResource::collection($this->lessons),
            'total_question' => $totalQuestion,
            'percentage_easy' => $perEasy,
            'percentage_medium' => $perMedium,
            'percentage_hard' => $perHard,
            'is_bought' => $isBought,
        ];
    }

    private function getPercentage($num, $total)
    {
        if ($total == 0) {
            return 0;
        }
        return round(($num / $total) * 100 ?? 0, 2);
    }
}
