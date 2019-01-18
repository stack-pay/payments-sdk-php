<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait PaymentPlanTransform
{
    public function requestCopyPaymentPlan($transaction)
    {
    	$request = [
            'payment_plan_id'   => $transaction->object()->id(),
            'merchant_id'       => $transaction->object()->merchant()->id(),
    	];
    	if (!is_null($transaction->object()->splitMerchant())) {
			$request['split_merchant_id'] = $transaction->object()->splitMerchant()->id();
		}
    	if (!is_null($transaction->object()->paymentPriority())) {
            $request['payment_priority'] = $transaction->object()->paymentPriority();
		}
        $transaction->request()->body($request);
    }
}
