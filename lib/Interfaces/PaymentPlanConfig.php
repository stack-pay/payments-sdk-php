<?php

namespace StackPay\Payments\Interfaces;

interface PaymentPlanConfig
{
    public function months();
    public function day();
    public function gracePeriod();

    //-----------

    public function setMonths($months = null);
    public function setDay($day = null);
    public function setGracePeriod($gracePeriod = null);
}
