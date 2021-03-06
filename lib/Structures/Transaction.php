<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Transaction implements Interfaces\Transaction
{
    public $id;
    public $type = 'Undefined';
    public $order;
    public $customer;
    public $merchant;
    public $paymentMethod;
    public $amount;
    public $split;
    public $currency;
    public $authCode;
    public $status;
    public $avsCode;
    public $cvvResponseCode;
    public $invoiceNumber;
    public $externalID;
    public $comment1;
    public $comment2;
    public $softDescriptor;

    public function __construct($id = null)
    {
        $this->setId($id);
    }

    public function id()
    {
        return $this->id;
    }

    public function type()
    {
        return 'Undefined';
    }

    public function order()
    {
        return $this->order;
    }

    public function customer()
    {
        return $this->customer;
    }

    public function merchant()
    {
        return $this->merchant;
    }

    public function paymentMethod()
    {
        return $this->paymentMethod;
    }

    public function amount()
    {
        return $this->amount;
    }

    public function split()
    {
        return $this->split;
    }

    public function currency()
    {
        return $this->currency;
    }

    public function authCode()
    {
        return $this->authCode;
    }

    public function status()
    {
        return $this->status;
    }

    public function avsCode()
    {
        return $this->avsCode;
    }

    public function cvvResponseCode()
    {
         return $this->cvvResponseCode;
    }

    public function invoiceNumber()
    {
        return $this->invoiceNumber;
    }

    public function externalID()
    {
        return $this->externalID;
    }

    public function comment1()
    {
        return $this->comment1;
    }

    public function comment2()
    {
        return $this->comment2;
    }

    public function softDescriptor()
    {
        return $this->softDescriptor;
    }

    // ----

    public function setID($id = null)
    {
        $this->id = $id;

        return $this;
    }

    public function setOrder(Interfaces\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    public function setCustomer(Interfaces\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    public function setMerchant(Interfaces\Merchant $merchant = null)
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function setPaymentMethod(Interfaces\PaymentMethod $paymentMethod = null)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function setAmount($amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    public function setSplit(Interfaces\Split $split = null)
    {
        $this->split = $split;

        return $this;
    }

    public function setCurrency($currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    public function setAuthCode($authCode = null)
    {
        $this->authCode = $authCode;

        return $this;
    }

    public function setStatus($status = null)
    {
         $this->status = $status;

         return $this;
    }

    public function setAVSCode($avsCode = null)
    {
        $this->avsCode = $avsCode;

        return $this;
    }

    public function setCvvResponseCode($cvvResponseCode = null)
    {
        $this->cvvResponseCode = $cvvResponseCode;

        return $this;
    }

    public function setInvoiceNumber($invoiceNumber = null)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function setExternalID($externalID = null)
    {
        $this->externalID = $externalID;

        return $this;
    }

    public function setComment1($comment1 = null)
    {
        $this->comment1 = $comment1;

        return $this;
    }

    public function setComment2($comment2 = null)
    {
        $this->comment2 = $comment2;

        return $this;
    }

    public function setSoftDescriptor($softDescriptor = null)
    {
        $this->softDescriptor = $softDescriptor;

        return $this;
    }

    // ----

    public function createOrder()
    {
        if (! $this->order) {
            $this->order = new Order();
        }

        return $this->order;
    }

    public function createCustomer()
    {
        if (! $this->customer) {
            $this->customer = new Customer();
        }

        return $this->customer;
    }

    public function createMerchant()
    {
        if (! $this->merchant) {
            $this->merchant = new Merchant();
        }

        return $this->merchant;
    }

    public function createPaymentMethod()
    {
        if (! $this->paymentMethod) {
            $this->paymentMethod = new PaymentMethod();
        }

        return $this->paymentMethod;
    }

    public function createSplit()
    {
        if (! $this->split) {
            $this->split = new Split();
        }

        return $this->split;
    }
}
