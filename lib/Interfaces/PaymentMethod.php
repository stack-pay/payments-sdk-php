<?php

namespace StackPay\Payments\Interfaces;

interface PaymentMethod
{
    public function account();
    public function accountHolder();
    public function customer();
    public function id();

    // -----

    public function setAccount(      Account       $account       = null);
    public function setAccountHolder(AccountHolder $accountHolder = null);
    public function setCustomer(     Customer      $customer      = null);
    public function setID(                         $id            = null);

    //----

    public function createCustomer();
}
