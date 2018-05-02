<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Exceptions;

trait MerchantLinkTransform
{
    public function responseMerchantLink($transaction)
    {
        $transaction->object()->setLink(
            $transaction->response()->body()['URL']
        );
    }
}
