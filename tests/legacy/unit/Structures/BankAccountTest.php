<?php

use StackPay\Payments\Structures;

final class BankAccountTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\BankAccount::class;

    public function test_constructor()
    {
        $type                   = StackPay\Payments\AccountTypes::CHECKING;
        $account_number         = '001234567891';
        $routing_number         = '111000025';
        $save_payment_method    = true;

        $bank_account   = new $this->struct($type, $account_number, $routing_number, $save_payment_method);

        $this->assertEquals($type, $bank_account->type());
        $this->assertEquals($account_number, $bank_account->number());
        $this->assertEquals($routing_number, $bank_account->routingNumber());
        $this->assertEquals($save_payment_method, $bank_account->savePaymentMethod());
    }
}
