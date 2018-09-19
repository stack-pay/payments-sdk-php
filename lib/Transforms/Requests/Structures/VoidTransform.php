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
                    'Type'          => 'Void',
                    'InvoiceNumber' => $transaction->object()->invoiceNumber() ?: null,
                    'ExternalId'    => $transaction->object()->externalID() ?: null,
                    'Comment1'      => $transaction->object()->comment1() ?: null,
                    'Comment2'      => $transaction->object()->comment2() ?: null,
                ]
            ]
        ];

        $transaction->request()->body($body);
    }
}
