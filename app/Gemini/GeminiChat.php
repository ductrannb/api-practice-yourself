<?php

namespace App\Gemini;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;


class GeminiChat extends BaseModel
{
    use HasUuids;

    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Str::uuid();
            }
        });
    }

    public function setPartsAttribute($value): void
    {
        if (!is_array($value)) {
            $value = [];
        }
        $this->attributes['parts'] = json_encode($value);
    }

    public function getPartsAttribute()
    {
        return json_decode($this->attributes['parts'] ?? '', true) ?? [];
    }
}
