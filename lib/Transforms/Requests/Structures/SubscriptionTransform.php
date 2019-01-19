<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait SubscriptionTransform
{
    public function requestCreateSubscription($transaction)
    {
        $body = [
            'payment_method'    => [
                'method'            => $transaction->object()->paymentMethod()->token()
            ],
            'external_id'           => $transaction->object()->externalID(),
            'amount'                => $transaction->object()->amount(),
            'split_amount'          => $transaction->object()->splitAmount(),
            'down_payment_amount'   => $transaction->object()->paymentPlan()->downPaymentAmount(),
            'day'                   => $transaction->object()->paymentPlan()->day()
        ];
        $transaction->request()->body($body);
    }
}
