<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class PaymentPlanInstallment implements Interfaces\PaymentPlanInstallment
{
    public $date;
    public $percentage;
    public $interval;

    public function date()
    {
        return $this->date;
    }

    public function percentage()
    {
        return $this->percentage;
    }

    public function interval()
    {
        return $this->interval;
    }

    //----------

    public function setDate($date = null)
    {
        $this->date = $date;

        return $this;
    }

    public function setPercentage($percentage = null)
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function setInterval($interval = null)
    {
        $this->interval = $interval;

        return $this;
    }
}
