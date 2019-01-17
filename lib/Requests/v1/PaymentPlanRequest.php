<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class PaymentPlanRequest extends Request
{
    public $paymentPlan;

    public function __construct(Structures\PaymentPlan $paymentPlan)
    {
        parent::__construct();

        $this->paymentPlan = $paymentPlan;
    }

    public function copy()
    {
        $this->method   = 'POST';
        $this->endpoint = '/api/merchants/' . $this->paymentPlan->merchant->id . '/payment-plan';
        $this->hashKey  = $this->paymentPlan->merchant->hashKey;
        $this->body     = $this->restTranslator->buildPaymentPlanCopyElement($this->paymentPlan);

        return $this;
    }
}
