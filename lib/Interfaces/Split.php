<?php

namespace StackPay\Payments\Interfaces;

interface Split
{
    public function merchant();
    public function amount();

    // ------

    public function setMerchant(Merchant $merchant = null);
    public function setAmount($amount = null);

    // ---------

    public function createMerchant();
}
