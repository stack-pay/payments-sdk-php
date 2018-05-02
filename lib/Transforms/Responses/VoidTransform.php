<?php

namespace StackPay\Payments\Transforms\Responses;

trait VoidTransform
{
    public function responseVoid($transaction)
    {
        $body = $transaction->response()->body();
        $transaction->object()->setID($body['Transaction']);
        $transaction->object()->createMerchant()->setID($body['Merchant']);
        $transaction->object()->createVoidedTransaction()->setID($body['Void']['VoidedTransaction']);
        $transaction->object()->createPaymentMethod()->setID($body['Void']['PaymentMethod']);
        $transaction->object()->createCustomer()->setID($body['Void']['Customer']);
        $transaction->object()->createOrder()->setID($body['Order']);
        $transaction->object()->setStatus($body['Status']);
    }
}
