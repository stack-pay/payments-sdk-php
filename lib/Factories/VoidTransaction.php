<?php

namespace StackPay\Payments\Factories;

use StackPay\Payments\Interfaces;
use StackPay\Payments\Structures;

class VoidTransaction
{
    public static function previousTransaction(Interfaces\Transaction $originalTransaction)
    {
        return (new Structures\VoidTransaction())
            ->setOriginalTransaction($originalTransaction)
            ->setMerchant($originalTransaction->merchant());
    }
}
