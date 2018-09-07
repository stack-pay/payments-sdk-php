<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait VoidTransform
{
    public function requestVoid($transaction)
    {
        $body = [
            'Merchant'  => $transaction->object()->merchant()->id(),
            'Order'     => [
                'OriginalTransaction'   => $transaction->object()->originalTransaction()->id(),
                'Transaction'           => [
                    'Type'      => 'Void',
                    'Comment1'  => $transaction->object()->comment1(),
                    'Comment2'  => $transaction->object()->comment2(),
                ]
            ]
        ];

        $transaction->request()->body($body);
    }
}
