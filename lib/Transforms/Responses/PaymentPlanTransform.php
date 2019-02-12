<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Exceptions;
use StackPay\Payments\Structures;

trait PaymentPlanTransform
{
    public function responsePaymentPlan($transaction)
    {
        $body = $transaction->response()->body()['data'];

        $transaction->object()->setID($body['id']);
        $transaction->object()->setName($body['name']);
        $transaction->object()->setIsActive($body['is_active']);
        $transaction->object()->setRequestIncomingId($body['incoming_request_id']);
        $transaction->object()->setDownPaymentAmount($body['down_payment_amount']);
        $transaction->object()->setDownPaymentType($body['down_payment_type']);
        $transaction->object()->setMerchant((new Structures\Merchant())
            ->setID($body['merchant_id']));
        $planConfig = new Structures\PaymentPlanConfig();
        if (!empty($body['configuration']['months'])) {
            $planConfig->setMonths($body['configuration']['months']);
        }
        if (!empty($body['configuration']['installments'])) {
            $planConfig->setInstallments($body['configuration']['installments']);
        }
        if (!empty($body['configuration']['day'])) {
            $planConfig->setDay($body['configuration']['day']);
        }
        if (!empty($body['configuration']['grace_period'])) {
            $planConfig->setGracePeriod($body['configuration']['grace_period']);
        }
        $transaction->object()->setConfiguration($planConfig);
        if (!empty($body['split_merchant_id'])) {
            $transaction->object()->setSplitMerchant((new Structures\Merchant())
                ->setID($body['split_merchant_id']));
        }
        if (!empty($body['payment_priority'])) {
            $transaction->object()->setPaymentPriority($body['payment_priority']);
        }
    }

    public function responseMerchantPaymentPlans($transaction)
    {
        $body = $transaction->response()->body();
        $object = $transaction->object();

        $plans = [];
        foreach ($body['data'] as $planArray) {
            $planConfig = (new Structures\PaymentPlanConfig);
            if (isset($planArray['configuration']['months'])) {
                $planConfig->setMonths($planArray['configuration']['months']);
            }
            if (isset($planArray['configuration']['day'])) {
                $planConfig->setDay($planArray['configuration']['day']);
            }
            if (isset($planArray['configuration']['grace_period'])) {
                $planConfig->setGracePeriod($planArray['configuration']['grace_period']);
            }
            if (isset($planArray['configuration']['installments'])) {
                $planConfig->setInstallments($planArray['configuration']['installments']);
            }

            $plan = (new Structures\PaymentPlan())
                ->setID($planArray['id'])
                ->setName($planArray['name'])
                ->setIsActive($planArray['is_active'])
                ->setRequestIncomingId($planArray['incoming_request_id'])
                ->setDownPaymentAmount($planArray['down_payment_amount'])
                ->setDownPaymentType($planArray['down_payment_type'])
                ->setConfiguration($planConfig)
                ->setMerchant((new Structures\Merchant())
                    ->setID($planArray['merchant_id'])
                );

            if (!empty($planArray['split_merchant_id'])) {
                $plan->setSplitMerchant((new Structures\Merchant())
                    ->setID($planArray['split_merchant_id'])
                );
            }
            if (!empty($planArray['payment_priority'])) {
                $plan->setPaymentPriority($planArray['payment_priority']);
            }

            $plans[] = $plan;
        }

        $object
            ->setPlans($plans)
            ->setTotal($body['meta']['pagination']['total'])
            ->setCount($body['meta']['pagination']['count'])
            ->setPerPage($body['meta']['pagination']['per_page'])
            ->setCurrentPage($body['meta']['pagination']['current_page'])
            ->setTotalPages($body['meta']['pagination']['total_pages'])
            ->setLinks($body['meta']['pagination']['links']);
    }

    public function responseDefaultPaymentPlans($transaction)
    {
        $plans = [];
        foreach($transaction->response()->body()['data'] as $key => $value) {
            $plan = (new Structures\PaymentPlan())
                ->setID($value['id'])
                ->setName($value['name'])
                ->setIsActive($value['is_active'])
                ->setDownPaymentAmount($value['down_payment_amount'])
                ->setDownPaymentType($value['down_payment_type'])
                ->setConfiguration((new Structures\PaymentPlanConfig())
                    ->setMonths($value['configuration']['months'])
                );
            if (!empty($value['configuration']['day'])) {
                $plan->configuration()->setDay($value['configuration']['day']);
            }
            if (!empty($value['configuration']['grace_period'])) {
                $plan->configuration()->setGracePeriod($value['configuration']['grace_period']);
            }
            $plans[] = $plan;
        }
        $transaction->object()->setPlans($plans);
    }

    public function responsePaymentPlanSubscription($transaction)
    {
        $body = $transaction->response()->body()['data'];

        $transaction->object()->setID($body['id']);

        $downPaymentTransactionArr = $body['down_payment_transaction'];

        $downPayment = (new Structures\Transaction())
            ->setStatus($downPaymentTransactionArr['status'])
            ->setMerchant((new Structures\Merchant())
                ->setID($transaction->object()->paymentPlan()->merchant()->id())
            )
            ->setOrder((new Structures\Order())
                ->setID($downPaymentTransactionArr['order_id'])
            )
            ->setID($downPaymentTransactionArr['id'])
            ->setCustomer((new Structures\Customer())
                ->setID($downPaymentTransactionArr['payment_method']['customer_id'])
            )
            ->setPaymentMethod((new Structures\PaymentMethod())
                ->setID($downPaymentTransactionArr['payment_method']['id'])
            )
            ->setAmount($downPaymentTransactionArr['amount'])
            ->setCurrency($body['currency_code'])
            ->setInvoiceNumber($downPaymentTransactionArr['invoice_number'])
            ->setExternalID($downPaymentTransactionArr['external_id']);

            $paymentMethod = ((new Structures\PaymentMethod())
                ->setID($downPaymentTransactionArr['payment_method']['id'])
                ->setAccount((new Structures\Account())
                    ->setType($downPaymentTransactionArr['payment_method']['type'])
                    ->setLast4($downPaymentTransactionArr['payment_method']['account_last_four'])
                    ->setExpireMonth($downPaymentTransactionArr['payment_method']['expiration_month'])
                    ->setExpireYear($downPaymentTransactionArr['payment_method']['expiration_year'])
                )
                ->setAccountHolder((new Structures\AccountHolder())
                    ->setName($downPaymentTransactionArr['payment_method']['billing_name'])
                    ->setBillingAddress((new Structures\Address())
                        ->setAddress1($downPaymentTransactionArr['payment_method']['billing_address_1'])
                        ->setAddress2($downPaymentTransactionArr['payment_method']['billing_address_2'])
                        ->setCity($downPaymentTransactionArr['payment_method']['billing_city'])
                        ->setState($downPaymentTransactionArr['payment_method']['billing_state'])
                        ->setPostalCode($downPaymentTransactionArr['payment_method']['billing_zip'])
                        ->setCountry($downPaymentTransactionArr['payment_method']['billing_country'])
                    )
                )
            );
            
            if($downPaymentTransactionArr['payment_method']['customer_id']) {
                $paymentMethod->setCustomer((new Structures\Customer())
                    ->setID($downPaymentTransactionArr['payment_method']['customer_id']));
            }

            $downPayment->setPaymentMethod($paymentMethod);


        if (array_key_exists('split_merchant_id', $downPaymentTransactionArr)) {
            $downPayment->setSplit((new Structures\Split())
                ->setMerchant((new Structures\Merchant())
                    ->setID($downPaymentTransactionArr['split_merchant_id'])
                )
                ->setAmount($downPaymentTransactionArr['split_amount']));
        }

        if($downPaymentTransactionArr['payment_method']['method'] == 'credit_card') {
            $downPayment->paymentMethod()->setAccount((new Structures\Account())
                ->setType($downPaymentTransactionArr['payment_method']['type'])
                ->setLast4($downPaymentTransactionArr['payment_method']['account_last_four'])
                ->setExpireMonth($downPaymentTransactionArr['payment_method']['expiration_month'])
                ->setExpireYear($downPaymentTransactionArr['payment_method']['expiration_year'])
            );
        } elseif ($downPaymentTransactionArr['payment_method']['method'] == 'bank_account') {
            $downPayment->paymentMethod()->setAccount((new Structures\Account())
                ->setType($downPaymentTransactionArr['payment_method']['type'])
                ->setLast4($downPaymentTransactionArr['payment_method']['account_last_four'])
                ->setExpireMonth($downPaymentTransactionArr['payment_method']['routing_last_four'])
            );
        }

        $transaction->object()->setDownPaymentTransaction($downPayment);

        $scheduledTransactions = [];
        foreach ($body['scheduled_transactions'] as $scheduledTransactionArr) {
            $scheduledTransactions[] = (new Structures\ScheduledTransaction())
                ->setID($scheduledTransactionArr['id'])
                ->setMerchant($downPayment->merchant())
                ->setExternalId($scheduledTransactionArr['external_id'])
                ->setScheduledAt(new \DateTime($scheduledTransactionArr['scheduled_at']))
                ->setCurrencyCode($scheduledTransactionArr['currency_code'])
                ->setAmount($scheduledTransactionArr['amount'])
                ->setPaymentMethod((new Structures\PaymentMethod())
                    ->setID($scheduledTransactionArr['payment_method']['id'])
                    ->setAccount((new Structures\Account())
                        ->setType($scheduledTransactionArr['payment_method']['type'])
                        ->setLast4($scheduledTransactionArr['payment_method']['account_last_four'])
                        ->setExpireMonth($scheduledTransactionArr['payment_method']['expiration_month'])
                        ->setExpireYear($scheduledTransactionArr['payment_method']['expiration_year'])
                    )
                    ->setAccountHolder((new Structures\AccountHolder())
                        ->setName($scheduledTransactionArr['payment_method']['billing_name'])
                        ->setBillingAddress((new Structures\Address())
                            ->setAddress1($scheduledTransactionArr['payment_method']['billing_address_1'])
                            ->setAddress2($scheduledTransactionArr['payment_method']['billing_address_2'])
                            ->setCity($scheduledTransactionArr['payment_method']['billing_city'])
                            ->setState($scheduledTransactionArr['payment_method']['billing_state'])
                            ->setPostalCode($scheduledTransactionArr['payment_method']['billing_zip'])
                            ->setCountry($scheduledTransactionArr['payment_method']['billing_country'])
                        )
                    )
                );
        }
        $transaction->object()
            ->setScheduledTransactions($scheduledTransactions)
            ->setPaymentMethod($downPayment->paymentMethod());

        if (array_key_exists('split_merchant_id', $body)) {
            $transaction->object()->setSplitMerchant((new Structures\Merchant())
                ->setID($body['split_merchant_id'])
            );
        }
    }
}
