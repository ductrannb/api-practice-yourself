<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'courseChart' => new DashboardChartResource($this->courseChart),
            'examChart' => new DashboardChartResource($this->examChart),
            'mainChart' => [
                'labels' => $this->mainChart->labels ?? [],
                'question_data' => $this->mainChart->question_data ?? [],
                'correct_rate_data' => $this->mainChart->correct_rate_data ?? [],
            ]
        ];
    }
}
