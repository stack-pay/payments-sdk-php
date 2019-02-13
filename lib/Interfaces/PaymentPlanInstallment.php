<?php

namespace StackPay\Payments\Interfaces;

interface PaymentPlanInstallment
{
    public function date();
    public function percentage();
    public function interval();

    //----------

    public function setDate($date = null);
    public function setPercentage($percentage = null);
    public function setInterval($interval = null);
}
