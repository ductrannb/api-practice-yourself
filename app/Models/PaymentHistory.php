<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaymentHistory extends BaseModel
{
    use HasUuids;

    protected $casts = [
        'transactions' => 'array'
    ];
}
