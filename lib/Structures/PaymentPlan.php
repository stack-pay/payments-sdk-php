<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class PaymentPlan implements Interfaces\PaymentPlan
{
    public $id;
    public $name;
    public $requestIncomingId;
    public $downPaymentAmount;
    public $splitMerchant;
    public $merchant;
    public $configuration;
    public $paymentPriority;

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        return $this->name;
    }
    
    public function requestIncomingId()
    {
        return $this->requestIncomingId;
    }
    
    public function downPaymentAmount()
    {
        return $this->downPaymentAmount;
    }
    
    public function splitMerchant()
    {
        return $this->splitMerchant;
    }
    
    public function merchant()
    {
        return $this->merchant;
    }
    
    public function configuration()
    {
        return $this->configuration;
    }
    
    public function paymentPriority()
    {
        return $this->paymentPriority;
    }

    // --------

    public function setID($id = null)
    {
        $this->id = $id;

        return $this;
    }

    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    public function setRequestIncomingId($requestIncomingId = null)
    {
        $this->requestIncomingId = $requestIncomingId;

        return $this;
    }

    public function setDownPaymentAmount($downPaymentAmount = null)
    {
        $this->downPaymentAmount = $downPaymentAmount;

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

    public function setConfiguration(PaymentPlanConfig $config = null)
    {
        $this-> $config = $ $config;

        return $this;
    }

    public function setPaymentPriority($paymentPrioroty = null)
    {
        $this->paymentPrioroty = $paymentPrioroty;

        return $this;
    }
}
