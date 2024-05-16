<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardTeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'questionChart' => new DashboardChartResource($this->questionChart),
            'examChart' => new DashboardChartResource($this->examChart),
            'mainChart' => [
                'labels' => $this->mainChart->labels ?? [],
                'joiner_data' => $this->mainChart->joiner_data ?? [],
                'submit_time_data' => $this->mainChart->submit_time_data ?? [],
            ]
        ];
    }
}
