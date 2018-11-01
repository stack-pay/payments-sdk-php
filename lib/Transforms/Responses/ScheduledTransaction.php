<?php

namespace StackPay\Payments\Transforms\Responses;

trait ScheduledTransaction
{
    public function responseScheduledTransaction($transaction)
    {
        $body = $transaction->response()->body();

        $transaction->object()->setID($body['data']['id']);
    }

    public function responseGetScheduledTransaction($transaction)
    {
        $body = $transaction->response()->body();

        $transaction->object()->setStatus($body['data']['status']);
        $transaction->object()->createMerchant()->setID($body['data']['merchant_id']);
        $transaction->object()->setScheduledAt(new \DateTime($body['data']['scheduled_at']));
        $transaction->object()->setCurrencyCode($body['data']['currency_code']);
        $transaction->object()->setAmount($body['data']['amount']);

        $transaction->object()->createPaymentMethod()->setID($body['data']['payment_method']['id'])
            ->createCustomer()->setID($body['data']['payment_method']['customer_id']);

        $transaction->object()->paymentMethod()->createAccountHolder()->createBillingAddress()
            ->setAddress1($body['data']['payment_method']['billing_address_1'])
            ->setAddress2(isset($body['data']['payment_method']['billing_address_2'])
                ? $body['data']['payment_method']['billing_address_2'] : '')
            ->setCity($body['data']['payment_method']['billing_city'])
            ->setState($body['data']['payment_method']['billing_state'])
            ->setPostalCode($body['data']['payment_method']['billing_zip'])
            ->setCountry($body['data']['payment_method']['billing_country']);

        if ($body['data']['payment_method']['method'] == 'credit_card') {
            $transaction->object()->paymentMethod()->createAccount()
                ->setType($body['data']['payment_method']['type'])
                ->setExpireMonth($body['data']['payment_method']['expiration_month'])
                ->setExpireYear($body['data']['payment_method']['expiration_year'])
                ->setLast4($body['data']['payment_method']['account_last_four']);
        } elseif ($body['data']['payment_method']['method'] == 'bank_account') {
            $transaction->object()->paymentMethod()->createAccount()
                ->setType($body['data']['payment_method']['type'])
                ->setLast4($body['data']['payment_method']['account_last_four'])
                ->setRoutingLast4($body['data']['payment_method']['routing_last_four']);
        }

        if (array_key_exists('split_merchant_id', $body['data'])) {
            $transaction->object()
                ->createSplit()
                ->setAmount($body['data']['split_amount'])
                ->createMerchant()
                    ->setID($body['data']['split_merchant_id']);
        }
    }
}
