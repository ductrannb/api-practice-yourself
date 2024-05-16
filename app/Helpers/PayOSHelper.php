<?php

namespace App\Helpers;

use PayOS\PayOS;

class PayOSHelper
{
    public const DEFAULT_RETURN_ENDPOINT = '/payos/return';
    public const DEFAULT_CANCEL_ENDPOINT = '/payos/cancel';

    private $payos;

    public function __construct()
    {
        $this->payos = new PayOS(env('PAYOS_CLIENT_ID'), env('PAYOS_API_KEY'), env('PAYOS_CHECKSUM_KEY'));
    }

    public function createPaymentLink(
        int $orderCode, int $amount,
        string $description = null,
        string $returnEndpoint = null,
        string $cancelEndpoint = null
    ) {
        $data = [
            'orderCode' => $orderCode,
            'amount' => $amount,
            'description' => $description ?: 'Thanh toán tại ' . env('APP_NAME'),
            'returnUrl' => trim(env('APP_URL'), '/')
                . '/'
                . trim(($returnEndpoint ?: self::DEFAULT_RETURN_ENDPOINT), '/'),
            'cancelUrl' => trim(env('APP_URL'), '/')
                . '/'
                . trim(($cancelEndpoint ?: self::DEFAULT_CANCEL_ENDPOINT), '/'),
        ];

        return $this->payOS->createPaymentLink($data);
    }

    public function getPaymentLinkInfo(int $orderCode)
    {
        return $this->payOS->getPaymentLinkInfomation($orderCode);
    }

    public function cancelPaymentLink(int $orderCode)
    {
        return $this->payos->cancelPaymentLink($orderCode);
    }
}
