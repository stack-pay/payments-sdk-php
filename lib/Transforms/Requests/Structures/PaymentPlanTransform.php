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

    public function requestCreatePaymentPlanSubscription($transaction)
    {
        $body = [
            'external_id'           => (string)$transaction->object()->externalID(),
            'amount'                => $transaction->object()->amount(),
            'down_payment_amount'   => $transaction->object()->downPaymentAmount(),
            'currency_code'         => $transaction->object()->currencyCode(),
            'payment_method'        => [
                'method'            => 'id',
                'id'                => $transaction->object()->paymentMethod()->id(),
            ],
        ];

        if ($transaction->object()->splitAmount()) {
            $body['split_amount'] = $transaction->object()->splitAmount();
        }

        if ($transaction->object()->day()) {
            $body['day'] = $transaction->object()->day();
        }

        $transaction->request()->body($body);
    }
}
