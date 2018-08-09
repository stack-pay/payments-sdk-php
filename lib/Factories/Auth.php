<?php

namespace StackPay\Payments\Factories;

use StackPay\Payments\Interfaces;
use StackPay\Payments\Structures;

class Auth
{
    public static function withPaymentMethod(
        Interfaces\PaymentMethod $paymentMethod,
        Interfaces\Merchant      $merchant,
        $amount,
        Interfaces\Split         $split = null,
        $currency = null
    ) {
        $auth = (new Structures\Auth())
            ->setPaymentMethod($paymentMethod)
            ->setMerchant($merchant)
            ->setAmount($amount)
            ->setSplit($split);

        if ($currency) {
            $auth->setCurrency($currency);
        }

        return $auth;
    }

    public static function withAccountDetails(
        Interfaces\Account       $account,
        Interfaces\AccountHolder $accountHolder,
        Interfaces\Merchant      $merchant,
        $amount,
        Interfaces\Customer      $customer = null,
        Interfaces\Split         $split = null,
        $currency = null
    ) {
        $auth = (new Structures\Auth())
            ->setAccount($account)
            ->setAccountHolder($accountHolder)
            ->setMerchant($merchant)
            ->setAmount($amount)
            ->setCustomer($customer)
            ->setSplit($split);

        if ($currency) {
            $auth->setCurrency($currency);
        }

        return $auth;
    }
}
