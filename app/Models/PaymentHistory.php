<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaymentHistory extends BaseModel
{
    use HasUuids;

    const TYPE_RECHARGE = 1;
    const TYPE_PURCHASE = 2;

    const STATUS_PENDING = 'PENDING';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_SUCCESS = 'SUCCESS';

    protected $casts = [
        'transactions' => 'array'
    ];
}
