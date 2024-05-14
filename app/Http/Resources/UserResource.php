<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'avatar' => $this->avatar,
            'email' => $this->email,
            'phone' => $this->phone,
            'birthday' => $this->birthday,
            'gender' => $this->gender,
            'role_id' => $this->role_id,
            'is_google_account' => $this->is_google_account,
            'created_at' => $this->created_at
        ];
    }
}
