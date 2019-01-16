<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait DefaultPaymentPlanTransform
{
    public function requestDefaultPaymentPlans($transaction)
    {
        $transaction->request()->body([]);
    }
}
