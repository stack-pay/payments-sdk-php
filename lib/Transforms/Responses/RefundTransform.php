<?php

namespace StackPay\Payments\Transforms\Responses;

trait RefundTransform
{
    public function responseRefund($transaction)
    {
        $body = $transaction->response()->body();
        $transaction->object()->setStatus($body['Status']);
        $transaction->object()->createMerchant()->setID($body['Merchant']);
        $transaction->object()->createOrder()->setID($body['Order']);
        $transaction->object()->setID($body['Transaction']);
        $transaction->object()->createCustomer()->setID($body['Refund']['Customer']);
        $transaction->object()->createPaymentMethod()->setID($body['Refund']['PaymentMethod']);
        $transaction->object()->createRefundedTransaction()->setID($body['Refund']['RefundedTransaction']);
        $transaction->object()->setAmount($body['Refund']['Amount']);
        $transaction->object()->setCurrency($body['Refund']['Currency']);

        if (array_key_exists('SplitMerchant', $body['Refund'])) {
            $transaction->object()->createSplit()->createMerchant()->setID(
                $body['Refund']['SplitMerchant']
            );
        }
    }
}
