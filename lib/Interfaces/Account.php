<?php

namespace StackPay\Payments\Interfaces;

interface Account
{
    public function savePaymentMethod();
    public function type();
    public function number();
    public function expireDate();
    public function cvv2();
    public function routingNumber();

    // ---------

    public function setSavePaymentMethod($savePaymentMethod = false);
    public function setType($type = null);
    public function setNumber($number = null);
    public function setExpireDate($expireDate = null);
    public function setCVV2($cvv2 = null);
    public function setRoutingNumber($routingNumber = null);
}
