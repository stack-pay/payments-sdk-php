<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\AccountTypes;
use StackPay\Payments\Interfaces;

class Account implements Interfaces\Account
{
    public $savePaymentMethod;
    public $type;
    public $number;
    public $expireDate;
    public $expireMonth;
    public $expireYear;
    public $cvv2;
    public $routingNumber;
    public $last4;
    public $routingLast4;

    public function savePaymentMethod()
    {
        return $this->savePaymentMethod;
    }

    public function type()
    {
        return $this->type;
    }

    public function number()
    {
        return $this->number;
    }

    public function expireDate()
    {
        return $this->expireDate;
    }

    public function expireMonth()
    {
        return $this->expireMonth;
    }

    public function expireYear()
    {
        return $this->expireYear;
    }

    public function cvv2()
    {
        return $this->cvv2;
    }

    public function routingNumber()
    {
        return $this->routingNumber;
    }

    public function last4()
    {
        return $this->last4;
    }

    public function routingLast4()
    {
        return $this->routingLast4;
    }

    // ---------

    public function setSavePaymentMethod($savePaymentMethod = false)
    {
        $this->savePaymentMethod = filter_var($savePaymentMethod, FILTER_VALIDATE_BOOLEAN);

        return $this;
    }

    public function setType($type = null)
    {
        $this->type = $type;

        return $this;
    }

    public function setNumber($number = null)
    {
        $this->number = $number;

        return $this;
    }

    public function setExpireDate($expireDate = null)
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    public function setExpireMonth($expireMonth = null)
    {
        $this->expireMonth = $expireMonth;

        return $this;
    }

    public function setExpireYear($expireYear = null)
    {
        $this->expireYear = $expireYear;

        return $this;
    }

    public function setCVV2($cvv2 = null)
    {
        $this->cvv2 = $cvv2;

        return $this;
    }

    public function setRoutingNumber($routingNumber = null)
    {
        $this->routingNumber = $routingNumber;

        return $this;
    }

    public function setLast4($last4 = null)
    {
        $this->last4 = $last4;

        return $this;
    }

    public function setRoutingLast4($routingLast4 = null)
    {
        $this->routingLast4 = $routingLast4;

        return $this;
    }

    // ---------

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
