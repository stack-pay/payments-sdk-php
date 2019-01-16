<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Exceptions;
use StackPay\Payments\Structures\PaymentPlan;

trait DefaultPaymentPlanTransform
{
    public function responseDefaultPaymentPlans($transaction)
    {
        $plans = [];
        foreach($transaction->response()->body()['data'] as $key => $value) {
            $plan = (new PaymentPlan())
                ->setID($value['id'])
                ->setName($value['name'])
                ->setRequestIncomingId($value['request_incoming_id'])
                ->setDownPaymentAmount($value['down_payment_amount']);
            if (!empty($value['split_merchant_id'])) {
                $plan->splitMerchant()->setID($value['split_merchant_id']);
            }
            if (!empty($value['configuration']['day'])) {
                $plan->configuration()->setDay($value['configuration']['day']);
            }
            if (!empty($value['payment_priority'])) {
                $plan->setPaymentPriority($value['payment_priority']);
            }
            $plan->merchant()->setID($value['merchant_id']);
            $plan->configuration()->setMonths($value['configuration']['months']);
            $plans[] = $plan;
        }
    }
}
