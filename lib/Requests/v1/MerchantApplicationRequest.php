<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class MerchantApplicationRequest extends Request
{
    protected $merchantApplication;

    public function __construct(Structures\MerchantApplication $merchantApplication)
    {
        parent::__construct();

        $this->merchantApplication = $merchantApplication;
    }

    public function open()
    {
        $this->method   = 'POST';
        $this->endpoint = '/api/merchants/link';
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = $this->translator->buildMerchantApplicationElement($this->merchantApplication);

        return $this;
    }

    public function cancel()
    {
        $this->method   = 'DELETE';
        $this->endpoint = '/api/merchant-applications/'. $this->merchantApplication->token;
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = null;

        return $this;
    }
}
