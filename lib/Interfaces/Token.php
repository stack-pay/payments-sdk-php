<?php

namespace StackPay\Payments\Interfaces;

interface Token extends PaymentMethod
{
    public function token();

    // -----

    public function setToken($token = null);
}
