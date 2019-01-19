<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class PaymentPlanConfig implements Interfaces\PaymentPlanConfig
{
    public $months;
    public $day;

    public function months()
    {
        return $this->months;
    }

    public function day()
    {
        return $this->day;
    }

    // --------

    public function setMonths($months = null)
    {
        $this->months = $months;

        return $this;
    }

    public function setDay($day = null)
    {
        $this->day = $day;

        return $this;
    }
}
