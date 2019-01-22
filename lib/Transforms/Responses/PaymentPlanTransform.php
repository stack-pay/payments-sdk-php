<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Exceptions;
use StackPay\Payments\Structures;

trait PaymentPlanTransform
{
    public function responseCopyPaymentPlan($transaction)
    {
        $body = $transaction->response()->body()['data'];

        $transaction->object()->setID($body['id']);
        $transaction->object()->setName($body['name']);
        $transaction->object()->setRequestIncomingId($body['request_incoming_id']);
        $transaction->object()->setDownPaymentAmount($body['down_payment_amount']);
        $transaction->object()->setMerchant((new Structures\Merchant())
            ->setID($body['merchant_id']));
        $transaction->object()->setConfiguration((new Structures\PaymentPlanConfig())
            ->setMonths($body['configuration']['months']));
        if (!empty($body['split_merchant_id'])) {
            $transaction->object()->setSplitMerchant((new Structures\Merchant())
                ->setID($body['split_merchant_id']));
        }
        if (!empty($body['configuration']['day'])) {
            $transaction->object()->configuration()->setDay($body['configuration']['day']);
        }
        if (!empty($body['payment_priority'])) {
            $transaction->object()->setPaymentPriority($body['payment_priority']);
        }
    }

    public function responseMerchantPaymentPlans($transaction)
    {
        $body = $transaction->response()->body();
        $object = $transaction->object();

        $plans = [];
        foreach ($body['data'] as $planArray) {
            $planConfig = (new Structures\PaymentPlanConfig)
                ->setMonths($planArray['configuration']['months']);
            if (isset($planArray['configuration']['day'])) {
                $planConfig->setDay($planArray['configuration']['day']);
            }

            $plan = (new Structures\PaymentPlan())
                ->setID($planArray['id'])
                ->setName($planArray['name'])
                ->setRequestIncomingId($planArray['request_incoming_id'])
                ->setDownPaymentAmount($planArray['down_payment_amount'])
                ->setConfiguration($planConfig)
                ->setMerchant((new Structures\Merchant())
                    ->setID($planArray['merchant_id'])
                );

            if (!empty($planArray['split_merchant_id'])) {
                $plan->setSplitMerchant((new Structures\Merchant())
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

    public function responseDefaultPaymentPlans($transaction)
    {
        $plans = [];
        foreach($transaction->response()->body()['data'] as $key => $value) {
            $plan = (new Structures\PaymentPlan())
                ->setID($value['id'])
                ->setName($value['name'])
                ->setRequestIncomingId($value['request_incoming_id'])
                ->setDownPaymentAmount($value['down_payment_amount'])
                ->setMerchant((new Structures\Merchant())
                    ->setID($value['merchant_id'])
                )
                ->setConfiguration((new Structures\PaymentPlanConfig())
                    ->setMonths($value['configuration']['months'])
                );
            if (!empty($value['split_merchant_id'])) {
                $plan->setSplitMerchant((new Structures\Merchant())
                    ->setID($value['split_merchant_id'])
                );
            }
            if (!empty($value['configuration']['day'])) {
                $plan->configuration()->setDay($value['configuration']['day']);
            }
            if (!empty($value['payment_priority'])) {
                $plan->setPaymentPriority($value['payment_priority']);
            }
            $plans[] = $plan;
        }
        $transaction->object()->setPlans($plans);
    }

    public function responseCreatePaymentPlanSubscription($transaction)
    {
        $body = $transaction->response()->body()['data'];

        $transaction->object()->setID($body['id']);

        $initial = new Structures\Transaction(); // TODO: Needs updated
        $transaction->object()->setInitialTransaction($initial);

        $scheduledTransactions = [];
        foreach ($body['scheduled_transactions'] as $scheduledTransactionArr) {
            $scheduledTransactions[] = new Structures\ScheduledTransaction(); // TODO: Needs updated
        }
        $transaction->object()->setScheduledTransactions($scheduledTransactions);
    }
}
