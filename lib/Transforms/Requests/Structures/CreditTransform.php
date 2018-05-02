<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait CreditTransform
{
    public function requestCredit($transaction)
    {
        $body = [
            'Merchant'  => $transaction->object()->merchant()->id(),
            'Order'     => [
                'PaymentMethod' => $transaction->object()->paymentMethod()->id(),
                'Transaction'           => [
                    'Type'     => 'Credit',
                    'Amount'   => $transaction->object()->amount(),
                    'Currency' => $transaction->object()->currency(),
                ]
            ]
        ];

        $transaction->request()->body($body);
    }
}
