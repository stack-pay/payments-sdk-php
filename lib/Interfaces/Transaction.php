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
    public function invoiceNumber();
    public function externalID();
    public function comment1();
    public function comment2();
    public function softDescriptor();

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
    public function setInvoiceNumber($invoiceNumber = null);
    public function setExternalID($externalID = null);
    public function setComment1($comment1 = null);
    public function setComment2($comment2 = null);
    public function setSoftDescriptor($softDescriptor = null);

    //------

    public function createOrder();
    public function createCustomer();
    public function createMerchant();
    public function createPaymentMethod();
    public function createSplit();
}
