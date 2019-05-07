<?php

namespace StackPay\Payments\Factories;

use StackPay\Payments\Interfaces;
use StackPay\Payments\Structures;

class Sale
{
    public static function withPaymentMethod(
        Interfaces\PaymentMethod $paymentMethod,
        Interfaces\Merchant      $merchant,
        $amount,
        Interfaces\Split         $split = null,
        $currency = null,
        $softDescriptor = null
    ) {
        $sale = (new Structures\Sale())
            ->setPaymentMethod($paymentMethod)
            ->setMerchant($merchant)
            ->setAmount($amount)
            ->setSplit($split)
            ->setSoftDescriptor($softDescriptor);

        if ($currency) {
            $sale->setCurrency($currency);
        }

        return $sale;
    }

    public static function withAccountDetails(
        Interfaces\Account       $account,
        Interfaces\AccountHolder $accountHolder,
        Interfaces\Merchant      $merchant,
        $amount,
        Interfaces\Customer      $customer = null,
        Interfaces\Split         $split = null,
        $currency = null,
        $softDescriptor = null
    ) {
        $sale = (new Structures\Sale())
            ->setAccount($account)
            ->setAccountHolder($accountHolder)
            ->setMerchant($merchant)
            ->setAmount($amount)
            ->setCustomer($customer)
            ->setSplit($split)
            ->setSoftDescriptor($softDescriptor);

        if ($currency) {
            $sale->setCurrency($currency);
        }

        return $sale;
    }

    public static function withMasterPass(
        $masterpassTransID,
        Interfaces\Merchant      $merchant,
        $amount,
        Interfaces\Customer      $customer = null,
        Interfaces\Split         $split = null,
        $currency = null
    ) {
        $sale = (new Structures\Sale())
            ->setMasterPassTransactionId($masterpassTransID)
            ->setMerchant($merchant)
            ->setAmount($amount)
            ->setCustomer($customer)
            ->setSplit($split);

        if ($currency) {
            $sale->setCurrency($currency);
        }

        return $sale;
    }
}
