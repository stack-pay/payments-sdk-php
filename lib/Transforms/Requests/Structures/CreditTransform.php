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
                'Transaction'   => [
                    'Type'          => 'Credit',
                    'Amount'        => $transaction->object()->amount(),
                    'Currency'      => $transaction->object()->currency(),
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
