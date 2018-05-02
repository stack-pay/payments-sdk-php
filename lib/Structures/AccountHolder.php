<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class AccountHolder implements Interfaces\AccountHolder
{
    public $name;
    public $billingAddress;

    public function name()
    {
        return $this->name;
    }

    public function billingAddress()
    {
        return $this->billingAddress;
    }

    //-----

    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    public function setBillingAddress(Interfaces\Address $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    //---

    public function createBillingAddress()
    {
        if (! $this->billingAddress) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress;
    }
}
