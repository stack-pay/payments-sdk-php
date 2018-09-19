<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait SaleTransform
{
    public function requestSale($transaction)
    {
        //
        // create base array
        //
        $body = [
            'Merchant' => $transaction->object()->merchant()->id(),
            'Order'    => [
                'Transaction'   => [
                    'Type'          => 'Sale',
                    'Currency'      => $transaction->object()->currency(),
                    'Amount'        => $transaction->object()->amount(),
                    'InvoiceNumber' => $transaction->object()->invoiceNumber() ?: null,
                    'ExternalId'    => $transaction->object()->externalID() ?: null,
                    'Comment1'      => $transaction->object()->comment1() ?: null,
                    'Comment2'      => $transaction->object()->comment2() ?: null,
                ]
            ]
        ];

        //
        // add payment method
        //
        if ($transaction->object()->account()) {
            $body['Order']['SavePaymentMethod'] = $transaction->object()->account()->savePaymentMethod();
            $body['Order']['Account']           = $this->requestAccount($transaction->object()->account());
            $body['Order']['AccountHolder']     = $this->requestAccountHolder($transaction->object()->accountHolder());
        }

        if ($transaction->object()->token()) {
            //@TODO - allow SavePaymentMethod here
            $body['Order']['Token'] = $transaction->object()->token()->token();
        }

        if ($transaction->object()->paymentMethod() &&
            $transaction->object()->paymentMethod()->id()
        ) {
            $body['Order']['PaymentMethod'] = $transaction->object()->paymentMethod()->id();
        }

        if ($transaction->object()->masterPassTransactionId()) {
            $body['Order']['MasterPass']['TransactionId'] = $transaction->object()->masterPassTransactionId();
        }

        //
        // optional split
        //
        if ($transaction->object()->split()) {
            $body['Order']['Transaction']['SplitAmount']   = $transaction->object()->split()->amount();
            $body['Order']['Transaction']['SplitMerchant'] = $transaction->object()->split()->merchant()->id();
        }

        //
        // optional customer
        //
        if ($transaction->object()->customer() &&
            $transaction->object()->customer()->ID()
        ) {
            $body['Order']['Customer'] = $transaction->object()->customer()->ID();
        }

        $transaction->request()->body($body);
    }
}
