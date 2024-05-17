<?php

namespace App\Http\Controllers;

use App\Helpers\PayOSHelper;
use App\Http\Requests\CreatePaymentLinkRequest;
use App\Models\PaymentHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private $payOSHelper;

    public function __construct(PayOSHelper $payOSHelper)
    {
        $this->payOSHelper = $payOSHelper;
    }

    /**
     * @throws \Exception
     */
    public function createPaymentLink(CreatePaymentLinkRequest $request)
    {
        $response = $this->payOSHelper->createPaymentLink($request->amount, $request->type);
        $info = $this->payOSHelper->getPaymentLinkInformation($response['orderCode']);
        PaymentHistory::create([
            'order_code' => $info['orderCode'],
            'amount' => $info['amount'],
            'amount_paid' => $info['amountPaid'],
            'amount_remaining' => $info['amountRemaining'],
            'status' => $info['status'],
            'type' => $request->type,
            'transactions' => $info['transactions'],
            'checkout_url' => $response['checkoutUrl'],
//            'expired_at' => Carbon::parse($response['expiredAt']) ?? null,
        ]);
        return $this->responseOk(data: [
            'checkout_url' => $response['checkoutUrl'],
            'return_url' => $this->payOSHelper->getUrl(PayOSHelper::DEFAULT_RETURN_ENDPOINT),
        ]);
    }

    public function callback(Request $request)
    {
        if ($request->success) {
            $data = $request->data;
            $this->payOSHelper->verifyWebhook($data);
            PaymentHistory::updateOrCreate([
                'order_code' => $data['orderCode']
            ], [
                'status' => PaymentHistory::STATUS_SUCCESS,
            ]);
        }
        return response()->json(['message' => 'Success']);
    }
}
