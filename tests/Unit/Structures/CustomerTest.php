<?php

declare(strict_types=1);

use StackPay\Payments\Structures;

final class CustomerTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Customer::class;

    public function test_id() { $this->full( "id", "int", false); }

}
