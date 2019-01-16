<?php

namespace StackPay\Payments\Interfaces;

interface PaymentPlanConfig
{
    public function months();
    public function day();

    //-----------

    public function setMonths($months = null);
    public function setDay($day = null);
}
