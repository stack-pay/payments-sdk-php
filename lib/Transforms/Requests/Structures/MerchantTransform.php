<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Exceptions;
use StackPay\Payments\Structures;

trait MerchantTransform
{
    public function responseHostedPageAccessToken($transaction)
    {
        $body = $transaction->response()->body();
        $transaction->object()->setID($body['id']);
        $transaction->object()->setAccessToken($body['access_token']);
    }
}
