<?php

namespace StackPay\Payments\Factories;

use StackPay\Payments\Interfaces;
use StackPay\Payments\Structures;

class Capture
{
    public static function previousTransaction(
        Interfaces\Transaction $originalTransaction,
        $amount,
        Interfaces\Split $split = null
    ) {
        return (new Structures\Capture())
            ->setOriginalTransaction($originalTransaction)
            ->setMerchant($originalTransaction->merchant())
            ->setAmount($amount)
            ->setSplit($split);
    }
}
