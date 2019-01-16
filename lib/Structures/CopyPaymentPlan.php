<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class CopyPaymentPlan implements Interfaces\CopyPaymentPlan
{
    public $paymentPlan;
    public $splitMerchant;
    public $merchant;
    public $paymentPriority;

    public function paymentPlan()
    {
        return $this->paymentPlan;
    }

    public function splitMerchant()
    {
        return $this->splitMerchant;
    }
    
    public function merchant()
    {
        return $this->merchant;
    }
    
    public function paymentPriority()
    {
        return $this->paymentPriority;
    }

    // --------

    public function setPaymentPlan(PaymentPlan $paymentPlan = null)
    {
        $this->paymentPlan = $paymentPlan;

        return $this;
    }

    public function setSplitMerchant(Merchant $merchant = null)
    {
        $this-> $merchant = $ $merchant;

        return $this;
    }

    public function setMerchant(Merchant $merchant = null)
    {
        $this-> $merchant = $ $merchant;

        return $this;
    }

    public function setPaymentPriority($paymentPrioroty = null)
    {
        $this->paymentPrioroty = $paymentPrioroty;

        return $this;
    }
}
