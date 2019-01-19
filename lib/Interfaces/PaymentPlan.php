<?php

namespace StackPay\Payments\Interfaces;

interface PaymentPlan
{
    public function id();
    public function name();
    public function requestIncomingId();
    public function downPaymentAmount();
    public function splitMerchant();
    public function merchant();
    public function configuration();
    public function paymentPriority();

    //-----------

    public function setID($id = null);
    public function setName($name = null);
    public function setRequestIncomingId($requestIncomingId = null);
    public function setDownPaymentAmount($downPaymentAmount = null);
    public function setSplitMerchant(Merchant $merchant = null);
    public function setMerchant(Merchant $merchant = null);
    public function setConfiguration(PaymentPlanConfig $config = null);
    public function setPaymentPriority($paymentPrioroty = null);
}
