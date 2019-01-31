<?php

namespace StackPay\Payments\Interfaces;

interface ScheduledTransaction
{
    public function id();
    public function merchant();
    public function paymentMethod();
    public function amount();
    public function scheduledAt();
    public function subscriptionID();

    //-------

    public function setID($id = null);
    public function setMerchant(Merchant $merchant = null);
    public function setPaymentMethod(PaymentMethod $paymentMethod = null);
    public function setAmount($amount = null);
    public function setScheduledAt(\DateTime $date = null);
    public function setSplit(Split $split = null);
    public function setSubscriptionID($subscriptionId = null);

    //------

    public function createMerchant();
    public function createPaymentMethod();
    public function createSplit();
}
