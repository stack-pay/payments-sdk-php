<?php

namespace StackPay\Payments\Interfaces;

interface Credit
{
    public function id();
    public function type();
    public function order();
    public function customer();
    public function merchant();
    public function paymentMethod();
    public function amount();
    public function currency();
    public function status();

    //-------

    public function setID($id = null);
    public function setOrder(Order $order = null);
    public function setCustomer(Customer $customer = null);
    public function setMerchant(Merchant $merchant = null);
    public function setPaymentMethod(PaymentMethod $paymentMethod = null);
    public function setAmount($amount = null);
    public function setSplit(Split $split = null);
    public function setStatus($status = null);

    //------

    public function createOrder();
    public function createCustomer();
    public function createMerchant();
    public function createPaymentMethod();
}
