<?php

namespace StackPay\Payments\Transforms\Responses;

trait AuthTransform
{
    public function responseAuth($transaction)
    {
        $body = $transaction->response()->body();

        $transaction->object()->setStatus($body['Status']);
        $transaction->object()->createMerchant()->setID($body['Merchant']);
        $transaction->object()->createOrder()->setID($body['Order']);
        $transaction->object()->setID($body['Transaction']);
        $transaction->object()->createCustomer()->setID($body['Payment']['Customer']);
        $transaction->object()->setAmount($body['Payment']['Amount']);
        $transaction->object()->setCurrency($body['Payment']['Currency']);
        $transaction->object()->setAuthCode($body['Payment']['AuthorizationCode']);
        $transaction->object()->setAVSCode($body['Payment']['AVSCode']);
        $transaction->object()->setCvvResponseCode($body['Payment']['CVVResponseCode']);

        $transaction->object()
            ->createPaymentMethod()
                ->setID($body['PaymentMethod']['ID'])
                ->createAccount()
                    ->setType($body['PaymentMethod']['AccountType'])
                    ->setLast4($body['PaymentMethod']['AccountLast4'])
                    ->setRoutingLast4($this->getIfExists($body['PaymentMethod'], 'RoutingLast4'))
                    ->setExpireMonth($this->getIfExists($body['PaymentMethod'], 'ExpirationMonth'))
                    ->setExpireYear($this->getIfExists($body['PaymentMethod'], 'ExpirationYear'));
        $transaction->object()
            ->createPaymentMethod()
                ->createAccountHolder()
                    ->createBillingAddress()
                        ->setAddress1($body['PaymentMethod']['BillingAddress']['AddressLine1'])
                        ->setAddress2($body['PaymentMethod']['BillingAddress']['AddressLine2'])
                        ->setCity($body['PaymentMethod']['BillingAddress']['City'])
                        ->setState($body['PaymentMethod']['BillingAddress']['State'])
                        ->setPostalCode($body['PaymentMethod']['BillingAddress']['Zip'])
                        ->setCountry($body['PaymentMethod']['BillingAddress']['Country']);


        if (array_key_exists('SplitMerchant', $body['Payment'])) {
            $transaction->object()
                ->createSplit()
                    ->setAmount($body['Payment']['SplitAmount'])
                    ->createMerchant()
                        ->setId($body['Payment']['SplitMerchant']);
        }
    }
}
