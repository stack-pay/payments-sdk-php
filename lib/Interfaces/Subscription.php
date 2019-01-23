<?php

namespace StackPay\Payments\Interfaces;

interface Subscription
{
    public function id();
    public function paymentMethod();
    public function paymentPlan();
    public function externalID();
    public function amount();
    public function splitAmount();
    public function downPaymentAmount();
    public function downPaymentTransaction();
    public function scheduledTransactions();
    public function day();
    public function currencyCode();

    //-----------

    public function setID($id = null);
    public function setPaymentMethod(PaymentMethod $paymentMethod = null);
    public function setPaymentPlan(PaymentPlan $paymentPlan = null);
    public function setExternalID($externalID = null);
    public function setAmount($amount = null);
    public function setSplitAmount($splitAmount = null);
    public function setDownPaymentAmount($downPaymentAmount = null);
    public function setDownPaymentTransaction(Transaction $downPaymentTransaction = null);
    public function setScheduledTransactions(array $scheduledTransactions = null);
    public function setDay($day = null);
    public function setCurrencyCode($currencyCode = null);
}
