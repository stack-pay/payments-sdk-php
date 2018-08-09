<?php

namespace StackPay\Payments\Interfaces;

interface MerchantApplication
{
    public function externalId();
    public function name();
    public function rate();
    public function token();

    // ---------

    public function setExternalId($externalId = null);
    public function setName($name = null);
    public function setRate($rate = null);
    public function setToken($token = null);
}
