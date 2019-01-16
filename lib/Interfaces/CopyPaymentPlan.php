<?php

namespace StackPay\Payments\Interfaces;

interface PaymentPlan
{
    public function paymentPlan();
    public function splitMerchant();
    public function merchant();
    public function paymentPriority();

    //-----------

    public function setPaymentPlan($id = null);
    public function setSplitMerchant(Merchant $merchant = null);
    public function setMerchant(Merchant $merchant = null);
    public function setPaymentPriority($paymentPrioroty = null);
}
