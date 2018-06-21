<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\AccountTypes;

class CardAccount extends Account
{
    public function __construct($type, $accountNumber, $expirationDate, $cvv2, $savePaymentMethod)
    {
        if (! in_array($type, [
            AccountTypes::AMEX,
            AccountTypes::DISCOVER,
            AccountTypes::MASTERCARD,
            AccountTypes::VISA,
        ])) {
            throw new \Exception('Invalid type submitted for card account.');
        }

        $this->setType($type);
        $this->setNumber($accountNumber);
        $this->setExpireDate($expirationDate);
        $this->setCVV2($cvv2);
        $this->setSavePaymentMethod($savePaymentMethod);
    }
}
