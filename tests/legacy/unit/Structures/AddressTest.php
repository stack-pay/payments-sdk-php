<?php

use StackPay\Payments\Structures;

final class AddressTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Address::class;

    public function test_address1()
    {
        $this->full('address1', 'string', false);
    }

    public function test_address2()
    {
        $this->full('address2', 'string', false);
    }

    public function test_city()
    {
        $this->full('city', 'string', false);
    }

    public function test_state()
    {
        $this->full('state', 'string', false);
    }

    public function test_postalCode()
    {
        $this->full('postalCode', 'string', false);
    }

    public function test_country()
    {
        $this->full('country', 'string', false);
    }
}
