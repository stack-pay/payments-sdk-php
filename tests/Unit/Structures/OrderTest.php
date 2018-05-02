<?php

declare(strict_types=1);

use StackPay\Payments\Structures;

final class OrderTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Order::class;

    public function test_id() { $this->full( "id", "int", false); }

}
