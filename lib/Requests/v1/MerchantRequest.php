<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class MerchantRequest extends Request
{
    public static function limits(Structures\Merchant $merchant)
    {
        $request = new self();

        $request->method   = 'POST';
        $request->endpoint = '/api/merchants/limits';
        $request->hashKey  = StackPay::$privateKey;
        $request->body     = [
            'Merchant' => $merchant->id,
        ];

        return $request;
    }

    public static function rates()
    {
        $request = new self();

        $request->method   = 'POST';
        $request->endpoint = '/api/merchants/rates';
        $request->hashKey  = StackPay::$privateKey;
        $request->body     = null;

        return $request;
    }
}
