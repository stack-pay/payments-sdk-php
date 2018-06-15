<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\AccountTypes;

class BankAccount extends Account
{
    public function __construct($type, $accountNumber, $routingNumber, $savePaymentMethod)
    {
        if (! in_array($type, [
            AccountTypes::CHECKING,
            AccountTypes::SAVINGS,
        ])) {
            throw new \Exception('Invalid type submitted for bank account.');
        }

        $this->setType($type);
        $this->setNumber($accountNumber);
        $this->setRoutingNumber($routingNumber);
        $this->setSavePaymentMethod($savePaymentMethod);
    }
}
