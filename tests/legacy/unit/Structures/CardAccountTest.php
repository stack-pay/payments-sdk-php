<?php

use StackPay\Payments\Structures;

final class CardAccountTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\CardAccount::class;

    public function test_constructor()
    {
        $type                   = StackPay\Payments\AccountTypes::VISA;
        $account_number         = '4111111111111111';
        $expiration_date        = '1125';
        $cvv                    = '123';
        $save_payment_method    = true;

        $card_account   = new $this->struct($type, $account_number, $expiration_date, $cvv, $save_payment_method);

        $this->assertEquals($type, $card_account->type());
        $this->assertEquals($account_number, $card_account->number());
        $this->assertEquals($expiration_date, $card_account->expireDate());
        $this->assertEquals($cvv, $card_account->cvv2());
        $this->assertEquals($save_payment_method, $card_account->savePaymentMethod());
    }
}
