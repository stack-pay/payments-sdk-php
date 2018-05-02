<?php

namespace StackPay\Payments\Factories;

use StackPay\Payments\Interfaces;
use StackPay\Payments\Structures;

class ScheduleTransaction
{
    public static function withPaymentMethod(
        Interfaces\PaymentMethod $paymentMethod,
        Interfaces\Merchant      $merchant,
        $amount,
        \DateTime                $scheduledAt,
        $currency_code,
        Interfaces\Split         $split = null
    )
    {
        $scheduledTransaction = (new Structures\ScheduledTransaction())
            ->setPaymentMethod($paymentMethod)
            ->setMerchant($merchant)
            ->setAmount($amount)
            ->setScheduledAt($scheduledAt)
            ->setCurrencyCode($currency_code)
            ->setSplit($split);

        return $scheduledTransaction;
    }

    public static function withAccountDetails(
        Interfaces\Account       $account,
        Interfaces\AccountHolder $accountHolder,
        Interfaces\Merchant      $merchant,
        $amount,
        \DateTime                $scheduledAt,
        $currency_code,
        Interfaces\Split         $split = null
    )
    {
        $scheduledTransaction = (new Structures\ScheduledTransaction())
            ->setAccount($account)
            ->setAccountHolder($accountHolder)
            ->setMerchant($merchant)
            ->setAmount($amount)
            ->setScheduledAt($scheduledAt)
            ->setCurrencyCode($currency_code)
            ->setSplit($split);

        return $scheduledTransaction;
    }

    public static function withToken(
        Interfaces\Token         $token,
        Interfaces\Merchant      $merchant,
        $amount,
        \DateTime                $scheduledAt,
        $currency_code,
        Interfaces\Split         $split = null
    )
    {
        $scheduledTransaction = (new Structures\ScheduledTransaction())
            ->setToken($token)
            ->setMerchant($merchant)
            ->setAmount($amount)
            ->setScheduledAt($scheduledAt)
            ->setCurrencyCode($currency_code)
            ->setSplit($split);

        return $scheduledTransaction;
    }
}
