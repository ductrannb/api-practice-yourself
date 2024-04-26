<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseResource extends JsonResource
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
            'price' => $this->price,
            'image' => $this->image_url,
            'sold' => $this->sold,
            'count_lesson' => 0,
            'count_question' => 0,
            'teachers' => UserResource::collection($this->teachers),
            'short_description' => $this->short_description,
            'description' => $this->description,
            'created_at' => $this->created_at
        ];
    }
}
