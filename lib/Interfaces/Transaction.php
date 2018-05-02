<?php

namespace StackPay\Payments\Interfaces;

interface Transaction
{
    public function id();
    public function type();
    public function order();
    public function customer();
    public function merchant();
    public function paymentMethod();
    public function amount();
    public function split();
    public function currency();
    public function authCode();
    public function status();
    public function avsCode();
    public function cvvResponseCode();

    //-------

    public function setID($id = null);
    public function setOrder(Order $order = null);
    public function setCustomer(Customer $customer = null);
    public function setMerchant(Merchant $merchant = null);
    public function setPaymentMethod(PaymentMethod $paymentMethod = null);
    public function setAmount($amount = null);
    public function setSplit(Split $split = null);
    public function setCurrency($currency = null);
    public function setAuthCode($authCode = null);
    public function setStatus($status = null);
    public function setAvsCode($avsCode = null);
    public function setCvvResponseCode($cvvResponseCode = null);

    //------

    public function createOrder();
    public function createCustomer();
    public function createMerchant();
    public function createPaymentMethod();
    public function createSplit();
}
