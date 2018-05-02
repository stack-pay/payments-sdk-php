<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait AccountHolderTransform
{
    public function requestAccountHolder($accountHolder)
    {
        return [
            'Name'           => $accountHolder->name(),
            'BillingAddress' => $this->requestBillingAddress($accountHolder->billingAddress())
        ];
    }
}
