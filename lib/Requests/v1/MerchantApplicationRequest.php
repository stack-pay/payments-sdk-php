<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class MerchantApplicationRequest extends Request
{
    public static function open(Structures\MerchantApplication $merchantApplication)
    {
        $request = new self();

        $request->method   = 'POST';
        $request->endpoint = '/api/merchants/link';
        $request->hashKey  = StackPay::$privateKey;
        $request->body     = $this->translator->buildMerchantApplicationElement($merchantApplication);

        return $request;
    }

    public static function cancel(Structures\MerchantApplication $merchantApplication)
    {
        $request = new self();

        $request->method   = 'DELETE';
        $request->endpoint = '/api/merchant-applications/'. $merchantApplication->token;
        $request->hashKey  = StackPay::$privateKey;
        $request->body     = null;

        return $request;
    }
}
