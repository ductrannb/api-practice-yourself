<?php

namespace App\Helpers;

use App\Models\PaymentHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use PayOS\PayOS;

class PayOSHelper
{
    public const DEFAULT_RETURN_ENDPOINT = 'api/payos/return';
    public const DEFAULT_CANCEL_ENDPOINT = 'api/payos/cancel';

    private $payOS;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!env('PAYOS_CLIENT_ID') || !env('PAYOS_API_KEY') || !env('PAYOS_CHECKSUM_KEY')) {
            throw new Exception('Missing payment configuration', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $this->payOS = new PayOS(env('PAYOS_CLIENT_ID'), env('PAYOS_API_KEY'), env('PAYOS_CHECKSUM_KEY'));
    }

    public function createPaymentLink(
        int $amount,
        int $type,
        string $description = null,
        string $returnEndpoint = null,
        string $cancelEndpoint = null
    ) {
        $data = [
            'orderCode' => $this->getOrderCode(),
            'amount' => $amount,
            'description' => $description ?: ($type == PaymentHistory::TYPE_RECHARGE ? 'Nạp tiền tại Practice' : 'Thanh toán tại Practice'),
            'returnUrl' => $this->getUrl(self::DEFAULT_RETURN_ENDPOINT, $returnEndpoint),
            'cancelUrl' => $this->getUrl(self::DEFAULT_CANCEL_ENDPOINT, $returnEndpoint),
            'buyerEmail' => auth()->user()->email,
            'expiredAt' => Carbon::now()->addMinutes(20)->getTimestamp()
        ];
        return $this->payOS->createPaymentLink($data);
    }

    /**
     * @throws Exception
     */
    public function getPaymentLinkInformation(int|string $orderCode): array
    {
        return $this->payOS->getPaymentLinkInformation($orderCode);
    }

    /**
     * @throws Exception
     */
    public function cancelPaymentLink(int|string $orderCode): array
    {
        return $this->payOS->cancelPaymentLink($orderCode);
    }

    public function verifyWebhook($data)
    {
        return $this->payOS->verifyPaymentWebhookData($data);
    }

    public function getOrderCode(): int
    {
        $orderCode = rand(100000, 99999999);
        if (PaymentHistory::where('order_code', $orderCode)->exists()) {
            return $this->getOrderCode();
        }
        return $orderCode;
    }

    public function getUrl(string $default, $returnEndpoint = null)
    {
        return 'https://api.ductran.site/api/hello';
        return trim(env('APP_URL'), '/') . '/' . trim(($returnEndpoint ?: $default), '/');
    }
}
