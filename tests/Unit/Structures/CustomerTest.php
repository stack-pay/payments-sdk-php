<?php

use StackPay\Payments\Structures;

final class CustomerTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Customer::class;

    public function test_constructor()
    {
        $id = 123;

        $customer = new $this->struct($id);

        $this->assertEquals($id, $customer->id());
    }

    public function test_id()
    {
        $this->full('id', 'int', false);
    }
}
