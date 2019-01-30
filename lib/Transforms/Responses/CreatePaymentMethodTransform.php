<?php

namespace StackPay\Payments\Transforms\Responses;

trait CreatePaymentMethodTransform
{
    public function responseCreatePaymentMethod($transaction)
    {
        $body = $transaction->response()->body();
        $transaction->object()->createCustomer()->setID($body['Customer']);
        $transaction->object()->setID($body['PaymentMethod']['ID']);

        $transaction->object()->setStatus($body['Status']);

        $transaction->object()->createAccount()
            ->setLast4($body['PaymentMethod']['AccountLast4'])
            ->setType($body['PaymentMethod']['AccountType'])
            ->setExpireMonth($this->getIfExists($body['PaymentMethod'],'ExpirationMonth'))
            ->setExpireYear($this->getIfExists($body['PaymentMethod'],'ExpirationYear'))
            ->setRoutingLast4($this->getIfExists($body['PaymentMethod'],'RoutingLast4'));
        $transaction->object()->createAccountHolder()->createBillingAddress()
            ->setAddress1($body['PaymentMethod']['BillingAddress']['AddressLine1'])
            ->setAddress2($body['PaymentMethod']['BillingAddress']['AddressLine2'])
            ->setCity($body['PaymentMethod']['BillingAddress']['City'])
            ->setState($body['PaymentMethod']['BillingAddress']['State'])
            ->setPostalCode($body['PaymentMethod']['BillingAddress']['Zip'])
            ->setCountry($body['PaymentMethod']['BillingAddress']['Country']);
    }
}
