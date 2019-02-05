<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Structures;

trait ScheduledTransactionTransform
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
        $transaction->object()->setSubscriptionID($body['data']['subscription_id']);

        $transaction->object()->createPaymentMethod()->setID($body['data']['payment_method']['id'])
            ->createCustomer()->setID($body['data']['payment_method']['customer_id']);

        $transaction->object()->paymentMethod()->createAccountHolder()->createBillingAddress()
            ->setAddress1($body['data']['payment_method']['billing_address_1'])
            ->setAddress2(isset($body['data']['payment_method']['billing_address_2']) ? $body['data']['payment_method']['billing_address_2'] : '')
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

        if (array_key_exists('split_merchant_id', $body['data'] )) {
            $transaction->object()
                ->createSplit()
                ->setAmount($body['data']['split_amount'])
                ->createMerchant()
                    ->setID($body['data']['split_merchant_id']);
        }
    }

    public function responseDailyScheduledTransactions($transaction)
    {
        $body = $transaction->response()->body();
        $object = $transaction->object();

        $scheduledTransactions = [];
        foreach ($body['data'] as $scheduledTransactionArray) {

            $scheduledTransaction = (new Structures\ScheduledTransaction())
                ->setID($scheduledTransactionArray['id'])
                ->setExternalID($scheduledTransactionArray['external_id'])
                ->setAmount($scheduledTransactionArray['amount'])
                ->setScheduledAt(new \DateTime($scheduledTransactionArray['scheduled_at']))
                ->setStatus($scheduledTransactionArray['status'])
                ->setCurrencyCode($scheduledTransactionArray['currency_code'])
                ->setSubscriptionID($scheduledTransactionArray['subscription'])
                ->setMerchant((new Structures\Merchant())
                    ->setID($scheduledTransactionArray['merchant_id'])
                );
            
            $scheduledTransaction->createPaymentMethod()
                ->setID($scheduledTransactionArray['payment_method']['id'])
                ->setMethod($scheduledTransactionArray['payment_method']['method']);

            $scheduledTransaction->paymentMethod()->createAccount()
                ->setSavePaymentMethod($scheduledTransactionArray['payment_method']['method'])
                ->setType($scheduledTransactionArray['payment_method']['type'])
                ->setRoutingLast4($scheduledTransactionArray['payment_method']['routing_last_four'])
                ->setLast4($scheduledTransactionArray['payment_method']['account_last_four'])
                ->setExpireMonth($scheduledTransactionArray['payment_method']['expiration_month'])
                ->setExpireYear($scheduledTransactionArray['payment_method']['expiration_year']);

            $scheduledTransaction->paymentMethod()->createCustomer()
                ->setID($scheduledTransactionArray['payment_method']['customer']['id'])
                ->setFirstName($scheduledTransactionArray['payment_method']['customer']['first_name'])
                ->setLastName($scheduledTransactionArray['payment_method']['customer']['last_name']);
            
            $scheduledTransaction->paymentMethod()->createAccountHolder()
                ->setName($scheduledTransactionArray['payment_method']['billing_name']);

            $scheduledTransaction->paymentMethod()->accountHolder()->createBillingAddress()
                ->setAddress1($scheduledTransactionArray['payment_method']['billing_address_1'])
                ->setAddress2($scheduledTransactionArray['payment_method']['billing_address_2'])
                ->setCity($scheduledTransactionArray['payment_method']['billing_city'])
                ->setState($scheduledTransactionArray['payment_method']['billing_state'])
                ->setPostalCode($scheduledTransactionArray['payment_method']['billing_zip'])
                ->setCountry($scheduledTransactionArray['payment_method']['billing_country']);

            if (!empty($scheduledTransactionArray['split_merchant_id'])) {
                $scheduledTransaction->createSplit()
                    ->setAmount($scheduledTransactionArray['split_amount'])
                    ->createMerchant()
                        ->setID($scheduledTransactionArray['split_merchant_id']);
            }

            if($scheduledTransactionArray['payment_method']['method'] == 'credit_card') {
                $scheduledTransaction->paymentMethod()->setAccount((new Structures\Account())
                    ->setType($scheduledTransactionArray['payment_method']['type'])
                    ->setLast4($scheduledTransactionArray['payment_method']['account_last_four'])
                    ->setExpireMonth($scheduledTransactionArray['payment_method']['expiration_month'])
                    ->setExpireYear($scheduledTransactionArray['payment_method']['expiration_year'])
                );
            } elseif ($scheduledTransactionArray['payment_method']['method'] == 'bank_account') {
                $scheduledTransaction->paymentMethod()->setAccount((new Structures\Account())
                    ->setType($scheduledTransactionArray['payment_method']['type'])
                    ->setLast4($scheduledTransactionArray['payment_method']['account_last_four'])
                    ->setRoutingLast4($scheduledTransactionArray['payment_method']['routing_last_four'])
                );
            }

            $scheduledTransactions[] = $scheduledTransaction;
        }

        $object
            ->setScheduledTransactions($scheduledTransactions)
            ->setTotal($body['meta']['pagination']['total'])
            ->setCount($body['meta']['pagination']['count'])
            ->setPerPage($body['meta']['pagination']['per_page'])
            ->setCurrentPage($body['meta']['pagination']['current_page'])
            ->setTotalPages($body['meta']['pagination']['total_pages'])
            ->setLinks($body['meta']['pagination']['links']);
    }
}
