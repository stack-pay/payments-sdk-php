<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class MultiplePaymentPlans implements Interfaces\MultiplePaymentPlans
{
    public $merchant;
    public $plans;

    public function merchant()
    {
        return $this->merchant;
    }

    public function plans()
    {
        return $this->plans;
    }

    // ---------

    public function setMerchant(Interfaces\Merchant $merchant = null)
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function setPlans(array $plans = null)
    {
        $this->plans = $plans;

        return $this;
    } 
}
