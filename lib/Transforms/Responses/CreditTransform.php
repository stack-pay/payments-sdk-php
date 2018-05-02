<?php

namespace StackPay\Payments\Transforms\Responses;

trait CreditTransform
{
    public function responseCredit($transaction)
    {
        $body = $transaction->response()->body();

        $transaction->object()
            ->setID($body['Transaction'])
            ->setStatus($body['Status'])
            ->setAmount($body['Credit']['Amount'])
            ->setCurrency($body['Credit']['Currency']);
        $transaction->object()->createMerchant()->setID($body['Merchant']);
        $transaction->object()->createOrder()->setID($body['Order']);
        $transaction->object()->createCustomer()->setID($body['Credit']['Customer']);
        $transaction->object()->createPaymentMethod()->setID($body['Credit']['PaymentMethod']);
    }
}
