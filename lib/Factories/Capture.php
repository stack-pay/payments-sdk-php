<?php

namespace StackPay\Payments\Factories;

use StackPay\Payments\Interfaces;
use StackPay\Payments\Structures;

class Capture
{
    public static function previousTransaction(
        Interfaces\Transaction $originalTransaction,
        $amount,
        Interfaces\Merchant $merchant = null,
        Interfaces\Split $split = null,
        $idempotencyKey = null
    )
    {
        return (new Structures\Capture())
            ->setOriginalTransaction($originalTransaction)
            ->setMerchant($merchant ? $merchant : $originalTransaction->merchant())
            ->setAmount($amount)
            ->setSplit($split);
    }
}
