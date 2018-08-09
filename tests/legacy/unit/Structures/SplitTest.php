<?php

use StackPay\Payments\Structures;

final class SplitTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Split::class;

    public function test_constructor()
    {
        $merchant   = new Structures\Merchant(123);
        $amount     = 12345;

        $split = new $this->struct($merchant, $amount);

        $this->assertEquals($merchant, $split->merchant());
        $this->assertEquals($amount, $split->amount());
    }

    public function test_merchant()
    {
        $this->full('merchant', Structures\Merchant::class, true);
    }

    public function test_amount()
    {
        $this->full('amount', 'int', false);
    }
}
