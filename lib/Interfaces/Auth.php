<?php

namespace StackPay\Payments\Interfaces;

interface Auth
{
    public function order();
    public function customer();
    public function amount();
    public function currency();
    public function account();
    public function accountHolder();
    public function split();
    public function token();
    public function authCode();
    public function softDescriptor();

    // ----

    public function setOrder(Order $order = null);
    public function setCustomer(Customer $customer = null);
    public function setAmount($amount = null);
    public function setCurrency($currency = null);
    public function setAccount(Account $account = null);
    public function setAccountHolder(AccountHolder $accountHolder = null);
    public function setSplit(Split $split = null);
    public function setToken(Token $token = null);
    public function setAuthCode($authCode = null);
    public function setSoftDescriptor($softDescriptor = null);

    // ----

    public function createSplit();
    public function createOrder();
    public function createCustomer();
}
