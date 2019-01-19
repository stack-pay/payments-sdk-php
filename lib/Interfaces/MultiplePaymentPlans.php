<?php

namespace StackPay\Payments\Interfaces;

interface MultiplePaymentPlans
{
    public function merchant();
    public function plans();

    // ---------

    public function setMerchant(Merchant $plans = null);
    public function setPlans(array $plans = null);
}
