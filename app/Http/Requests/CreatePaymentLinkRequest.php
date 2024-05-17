<?php

namespace App\Http\Requests;

use App\Models\PaymentHistory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentLinkRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|integer',
            'type' => ['required', 'integer', Rule::in([PaymentHistory::TYPE_RECHARGE, PaymentHistory::TYPE_PURCHASE])],
        ];
    }
}
