<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait RefundTransform
{
    public function requestRefund($transaction)
    {
        $body = [
            'Merchant'  => $transaction->object()->merchant()->id(),
            'Order'     => [
                'OriginalTransaction'   => $transaction->object()->originalTransaction()->id(),
                'Transaction'           => [
                    'Type'      => 'Refund',
                    'Amount'    => $transaction->object()->amount()
                ]
            ]
        ];

        if ($transaction->object()->split()) {
            $body['Order']['Transaction']['SplitAmount'] = $transaction->object()->split()->amount();

            if ($transaction->object()->split()->merchant()) {
                $body['Order']['Transaction']['SplitMerchant'] = $transaction->object()->split()->merchant()->id();
            }
        }

        $transaction->request()->body($body);
    }
}
