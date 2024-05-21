<?php

namespace App\Http\Controllers;

use App\Helpers\PayOSHelper;
use App\Http\Requests\CreatePaymentLinkRequest;
use App\Models\PaymentHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * @throws \Exception
     */
    public function cancelPayment(Request $request)
    {
        if ($request->code == '00') {
            $info = $this->payOSHelper->getPaymentLinkInformation($request->orderCode);
            $transaction = PaymentHistory::where('order_code', $request->orderCode)->first();
            $transaction->update([
                'status' => $info['status'],
            ]);
        }
        return $this->responseOk();
    }

    /**
     * @throws \Exception
     */
    public function returnPayment(Request $request)
    {
        if ($request->code == '00') {
            DB::transaction(function () use ($request) {
                $info = $this->payOSHelper->getPaymentLinkInformation($request->orderCode);
                $transaction = PaymentHistory::where('order_code', $request->orderCode)->first();
                $oldStatus = $transaction->status;
                $transaction->update([
                    'status' => $info['status'],
                ]);
                if ($info['status'] == PaymentHistory::STATUS_PAID && $oldStatus != PaymentHistory::STATUS_PAID) {
                    auth()->user()->update([
                        'balance' => auth()->user()->balance + $info['amountPaid'],
                    ]);
                }
            });
        }
        return $this->responseOk();
    }

    public function callback(Request $request)
    {
        DB::transaction(function() use ($request) {
            if ($request->success) {
                $data = $request->data;
                $this->payOSHelper->verifyWebhook($data);
                $transaction = PaymentHistory::where('order_code', $data['orderCode'])->first();
                $oldStatus = $transaction->status;
                $transaction->update([
                    'status' => PaymentHistory::STATUS_PAID,
                ]);
                if ($oldStatus != PaymentHistory::STATUS_PAID) {
                    auth()->user()->update([
                        'balance' => auth()->user()->balance + $data['amount'],
                    ]);
                    info('Payment callback: ' . $data['orderCode'] . ' -> ' . $data['amount']);
                }
                info('Payment callback success');
            }
        });
        return $this->responseOk('Success');
    }
}
