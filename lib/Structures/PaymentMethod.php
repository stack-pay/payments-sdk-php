<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\AccountTypes;
use StackPay\Payments\Interfaces;

class PaymentMethod implements Interfaces\PaymentMethod
{
    // standard
    public $id;
    public $customer;
    public $status;

    // V1Translator
    public $account;
    public $accountHolder;

    // V1RESTTranslator
    public $token;
    public $type;
    public $accountNumber;
    public $routingNumber;
    public $cvv2;
    public $expirationMonth;
    public $expirationYear;
    public $billingName;
    public $billingAddress1;
    public $billingAddress2;
    public $billingCity;
    public $billingState;
    public $billingZip;
    public $billingCountry;

    public function account()
    {
        return $this->account;
    }

    public function accountHolder()
    {
        return $this->accountHolder;
    }

    public function customer()
    {
        return $this->customer;
    }

    public function id()
    {
        return $this->id;
    }

    public function status()
    {
        return $this->status;
    }

    //-----

    public function setAccount(Interfaces\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    public function setAccountHolder(Interfaces\AccountHolder $accountHolder = null)
    {
        $this->accountHolder = $accountHolder;

        return $this;
    }

    public function setCustomer(Interfaces\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    public function setID($id = null)
    {
        $this->id = $id;

        return $this;
    }

    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    //----

    public function createAccount()
    {
        if (! $this->account) {
            $this->account = new Account();
        }

        return $this->account;
    }

    public function createAccountHolder()
    {
        if (! $this->accountHolder) {
            $this->accountHolder = new AccountHolder();
        }

        return $this->accountHolder;
    }

    public function createCustomer()
    {
        if (! $this->customer) {
            $this->customer = new Customer();
        }

        return $this->customer;
    }

    //----

    public function isBankAccount()
    {
        return in_array($this->type, [
            AccountTypes::CHECKING,
            AccountTypes::SAVINGS,
        ]);
    }

    public function isCardAccount()
    {
        return in_array($this->type, [
            AccountTypes::AMEX,
            AccountTypes::DISCOVER,
            AccountTypes::MASTERCARD,
            AccountTypes::VISA,
        ]);
    }
}
