<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'userChart' => new DashboardChartResource($this->userChart),
            'teacherChart' => new DashboardChartResource($this->teacherChart),
            'questionChart' => new DashboardChartResource($this->questionChart),
            'examChart' => new DashboardChartResource($this->examChart),
            'mainChart' => [
                'labels' => $this->mainChart->labels ?? [],
                'revenue_data' => $this->mainChart->revenue_data ?? [],
                'course_data' => $this->mainChart->course_data ?? [],
            ]
        ];
    }
}
