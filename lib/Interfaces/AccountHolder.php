<?php

namespace StackPay\Payments\Interfaces;

interface AccountHolder
{
    public function name();
    public function billingAddress();

    //-----

    public function setName($name = null);
    public function setBillingAddress(Address $billingAddress = null);

    //-----

    public function createBillingAddress();
}
