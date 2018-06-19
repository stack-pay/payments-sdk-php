<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\Exceptions;
use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class PaymentMethodRequest extends Request
{
    public static function create(Structures\Account $account, Structures\AccountHolder $accountHolder)
    {
        $request = new self();

        $request->method    = 'POST';
        $request->endpoint  = '/api/paymethods';
        $request->hashKey   = StackPay::$privateKey;
        $request->body      = [
            'Account'       => $this->translator->buildAccountElement($account),
            'AccountHolder' => $this->translator->buildAccountHolderElement($accountHolder),
        ];

        return $request;
    }

    public static function delete(Structures\PaymentMethod $paymentMethod)
    {
        $request = new self();

        $request->method   = 'DELETE';
        $request->endpoint = '/api/payment-methods/'. $paymentMethod->id;
        $request->hashKey  = StackPay::$privateKey;
        $request->body     = null;

        return $request;
    }

    public static function token(Structures\Token $token)
    {
        $request = new self();

        $request->method    = 'POST';
        $request->endpoint  = '/api/paymethods';
        $request->hashKey   = StackPay::$privateKey;
        $request->body      = $this->translator->buildTokenElement($token);

        return $request;
    }
}
