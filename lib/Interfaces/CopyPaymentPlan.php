<?php

namespace StackPay\Payments\Interfaces;

interface CopyPaymentPlan
{
    public function paymentPlan();
    public function splitMerchant();
    public function merchant();
    public function paymentPriority();

    //-----------

    public function setPaymentPlan(PaymentPlan $paymentPlan = null);
    public function setSplitMerchant(Merchant $splitMerchant = null);
    public function setMerchant(Merchant $merchant = null);
    public function setPaymentPriority($paymentPriority = null);
}
