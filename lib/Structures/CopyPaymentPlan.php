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

    public function setPaymentPlan(Interfaces\PaymentPlan $paymentPlan = null)
    {
        $this->paymentPlan = $paymentPlan;

        return $this;
    }

    public function setSplitMerchant(Interfaces\Merchant $splitMerchant = null)
    {
        $this->splitMerchant = $splitMerchant;

        return $this;
    }

    public function setMerchant(Interfaces\Merchant $merchant = null)
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function setPaymentPriority($paymentPriority = null)
    {
        $this->paymentPriority = $paymentPriority;

        return $this;
    }
}
