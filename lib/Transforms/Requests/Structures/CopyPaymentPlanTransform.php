<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait CopyPaymentPlanTransform
{
    public function requestCopyPaymentPlan($transaction)
    {
        $transaction->request()->body([
            'payment_plan_id'   => $transaction->object()->paymentPlan()->id(),
            'split_merchant_id' => $transaction->object()->splitMerchant()->id(),
            'merchant_id'       => $transaction->object()->merchant()->id(),
            'payment_priority'  => $transaction->object()->paymentPriority()->paymentPriority()
        ]);
    }
}
