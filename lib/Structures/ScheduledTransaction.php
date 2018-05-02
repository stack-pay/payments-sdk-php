<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Currency;
use StackPay\Payments\Interfaces;

class ScheduledTransaction extends Auth implements Interfaces\ScheduledTransaction
{
    public $id;
    public $customer;
    public $merchant;
    public $paymentMethod;
    public $amount;
    public $scheduledAt;
    public $currencyCode;
    public $split;
    public $type;

    public function id()
    {
        return $this->id;
    }

    public function customer()
    {
        return $this->customer;
    }

    public function merchant()
    {
        return $this->merchant;
    }

    public function paymentMethod()
    {
        return $this->paymentMethod;
    }

    public function amount()
    {
        return $this->amount;
    }

    public function currencyCode()
    {
        return $this->currencyCode;
    }

    public function scheduledAt()
    {
        return $this->scheduledAt;
    }

    public function split()
    {
        return $this->split;
    }

    public function type()
    {
        return 'ScheduledTransaction';
    }

    public function setID($id = null)
    {
        $this->id = $id;

        return $this;
    }

    public function setMerchant(Interfaces\Merchant $merchant = null)
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function setPaymentMethod(Interfaces\PaymentMethod $paymentMethod = null)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function setAmount($amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    public function setScheduledAt(\DateTime $date = null)
    {
        $this->scheduledAt = $date;

        return $this;
    }

    public function setCurrencyCode($currencyCode = null)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function setSplit(Interfaces\Split $split = null)
    {
        $this->split = $split;

        return $this;
    }

    public function createMerchant()
    {
        if (! $this->merchant) {
            $this->merchant = new Merchant();
        }

        return $this->merchant;
    }

    public function createPaymentMethod()
    {
        if (! $this->paymentMethod) {
            $this->paymentMethod = new PaymentMethod();
        }

        return $this->paymentMethod;
    }

    public function createSplit()
    {
        if (! $this->split) {
            $this->split = new Split();
        }

        return $this->split;
    }
}
