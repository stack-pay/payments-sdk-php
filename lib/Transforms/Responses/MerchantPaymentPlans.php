<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Exceptions;
use StackPay\Payments\Structures\Merchant;
use StackPay\Payments\Structures\PaymentPlan;
use StackPay\Payments\Structures\PaymentPlanConfig;

trait MerchantPaymentPlans
{
    public function responseMerchantPaymentPlans($transaction)
    {
        $body = $transaction->response()->body();
        $object = $transaction->object();

        $plans = [];
        foreach ($body['data'] as $planArray) {
            $planConfig = (new PaymentPlanConfig)
                ->setMonths($planArray['configuration']['months']);
            if (isset($planArray['configuration']['day'])) {
                $planConfig->setDay($planArray['configuration']['day']);
            }

            $plan = (new PaymentPlan())
                ->setID($planArray['id'])
                ->setName($planArray['name'])
                ->setRequestIncomingId($planArray['request_incoming_id'])
                ->setDownPaymentAmount($planArray['down_payment_amount'])
                ->setConfiguration($planConfig)
                ->setMerchant((new Merchant())
                    ->setID($planArray['merchant_id'])
                );

            if (!empty($planArray['split_merchant_id'])) {
                $plan->setSplitMerchant((new Merchant())
                    ->setID($planArray['split_merchant_id'])
                );
            }
            if (!empty($planArray['payment_priority'])) {
                $plan->setPaymentPriority($planArray['payment_priority']);
            }

            $plans[] = $plan;
        }

        $object
            ->setPlans($plans)
            ->setTotal($body['meta']['pagination']['total'])
            ->setCount($body['meta']['pagination']['count'])
            ->setPerPage($body['meta']['pagination']['per_page'])
            ->setCurrentPage($body['meta']['pagination']['current_page'])
            ->setTotalPages($body['meta']['pagination']['total_pages'])
            ->setLinks($body['meta']['pagination']['links']);
    }
}
