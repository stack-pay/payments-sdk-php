<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Exceptions;
use StackPay\Payments\Structures\Transaction;
use StackPay\Payments\Structures\ScheduledTransaction;

trait SubscriptionTransform
{
    public function responseCreateSubscription($transaction)
    {
        $body = $transaction->response()->body()['data'];

        $transaction->object()->subscription()->setID($body['id']);

        $initial = (new Transaction())
            ->setID($body['initial_transaction']['transaction'])
            ->setStatus($body['initial_transaction']['status'])
            ->createMerchant()->setID($body['initial_transaction']['merchant'])
            ->createOrder()->setID($body['initial_transaction']['order'])
            ->createCustomer()->setID($body['initial_transaction']['payment']['customer'])
            ->createPaymentMethod()->setID($body['initial_transaction']['payment']['paymentmethod'])
            ->setAmount($body['initial_transaction']['payment']['amount'])
            ->setCurrency($body['initial_transaction']['payment']['currency'])
            ->createSplit()
                ->setAmount($body['initial_transaction']['payment']['splitamount'])
                ->createMerchant()->setID($body['initial_transaction']['payment']['splitmerchant'])
            ->setInvoiceNumber($body['initial_transaction']['payment']['invoicenumber'])
            ->setExternalID($body['initial_transaction']['payment']['externalid'])
            ->setComment1($body['initial_transaction']['payment']['comment1'])
            ->setComment2($body['initial_transaction']['payment']['comment2'])
            ->setAuthCode($body['initial_transaction']['payment']['authorizationcode'])
            ->setAVSCode($body['initial_transaction']['payment']['avscode'])
            ->setCvvResponseCode($body['initial_transaction']['payment']['CVVResponseCode'])
            ->createPaymentMethod()
                ->setID($body['initial_transaction']['paymentMethod']['id'])
                ->createAccount()
                    ->setType($body['initial_transaction']['paymentmethod']['accounttype'])
                    ->setLast4($body['initial_transaction']['paymentmethod']['AccountLast4'])
                    ->setExpireMonth($body['initial_transaction']['paymentmethod']['ExpirationMonth'])
                    ->setExpireYear($body['initial_transaction']['paymentmethod']['ExpirationYear'])
                ->createAccountHolder()
                    ->setName($body['initial_transaction']['paymentmethod']['BillingName'])
                    ->createBillingAddress()
                        ->setAddress1($body['initial_transaction']['PaymentMethod']['BillingAddress']['AddressLine1'])
                        ->setAddress2($body['initial_transaction']['PaymentMethod']['BillingAddress']['AddressLine2'])
                        ->setCity($body['initial_transaction']['PaymentMethod']['BillingAddress']['City'])
                        ->setState($body['initial_transaction']['PaymentMethod']['BillingAddress']['State'])
                        ->setPostalCode($body['initial_transaction']['PaymentMethod']['BillingAddress']['Zip'])
                        ->setCountry($body['initial_transaction']['PaymentMethod']['BillingAddress']['Country'])
        ;

        $transaction->object()->subscription()->setInitialTransaction($initial);

        $scheduled = [];


        $transaction->object()->subscription()->setScheduledTransactions($scheduled);
    }
}
