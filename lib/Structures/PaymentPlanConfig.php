<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class PaymentPlanConfig implements Interfaces\PaymentPlanConfig
{
    public $months;
    public $day;
    public $gracePeriod;

    public function months()
    {
        return $this->months;
    }

    public function day()
    {
        return $this->day;
    }

    public function gracePeriod()
    {
        return $this->gracePeriod;
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

    public function setGracePeriod($gracePeriod = null)
    {
        $this->gracePeriod = $gracePeriod;

        return $this;
    }
}
