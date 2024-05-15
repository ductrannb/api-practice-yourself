<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardChartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'value' => $this->value ?? 0,
            'grow_percent' => $this->grow_percent ?? 0,
            'chart_labels' => $this->chart_labels ?? [],
            'chart_data' => $this->chart_data ?? []
        ];
    }
}
