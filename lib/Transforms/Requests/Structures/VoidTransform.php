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
                    'Type' => 'Void',
                ]
            ]
        ];

        $transaction->request()->body($body);
    }
}
