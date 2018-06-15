<?php

use StackPay\Payments\Structures;

final class AccountHolderTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\AccountHolder::class;

    public function test_constructor()
    {
        $name               = 'Billy Tester';
        $billing_address    = new Structures\Address(
            '5360 Legacy Drive',
            'Suite 150',
            'Plano',
            'TX',
            '75024'
        );

        $account_holder = new $this->struct($name, $billing_address);

        $this->assertEquals($name, $account_holder->name());
        $this->assertEquals($billing_address, $account_holder->billingAddress());
    }

    public function test_name()
    {
        $this->full('name', 'string', false);
    }

    public function test_billingAddress()
    {
        $this->full('billingAddress', Structures\Address::class, true);
    }
}
