<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\Exceptions;
use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class PaymentMethodRequest extends Request
{
    public $paymentMethod;

    public function __construct(Structures\PaymentMethod $paymentMethod)
    {
        parent::__construct();

        $this->paymentMethod = $paymentMethod;
    }

    public function create()
    {
        $this->method   = 'POST';
        $this->endpoint = '/api/paymethods';
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = [
            'Account'       => $this->translator->buildAccountElement($this->paymentMethod->account),
            'AccountHolder' => $this->translator->buildAccountHolderElement($this->paymentMethod->accountHolder),
        ];

        return $this;
    }

    public function delete()
    {
        $this->method   = 'DELETE';
        $this->endpoint = '/api/payment-methods/'. $this->paymentMethod->id;
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = null;

        return $this;
    }

    public function token()
    {
        $this->method    = 'POST';
        $this->endpoint  = '/api/paymethods';
        $this->hashKey   = StackPay::$privateKey;
        $this->body      = $this->translator->buildTokenElement($this->paymentMethod->token);

        return $this;
    }
}
