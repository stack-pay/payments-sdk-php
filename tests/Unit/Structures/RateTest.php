<?php

declare(strict_types=1);

use StackPay\Payments\Structures;

final class RateTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Rate::class;

    public function test_feeRate() { $this->full( "feeRate", "string", false); }
    public function test_feeTransaction() { $this->full( "feeTransaction", "string", false); }
    public function test_feeNotes() { $this->full( "feeNotes", "string", false); }
}
