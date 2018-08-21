<?php

use StackPay\Payments\Structures;

final class RateTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Rate::class;

    public function test_bankFeeRate()
    {
        $this->full('bankFeeRate', 'string', false);
    }

    public function test_bankFeeTransaction()
    {
        $this->full('bankFeeTransaction', 'string', false);
    }

    public function test_bankFeeNotes()
    {
        $this->full('bankFeeNotes', 'string', false);
    }

    public function test_cardFeeRate()
    {
        $this->full('cardFeeRate', 'string', false);
    }

    public function test_cardFeeTransaction()
    {
        $this->full('cardFeeTransaction', 'string', false);
    }

    public function test_cardFeeNotes()
    {
        $this->full('cardFeeNotes', 'string', false);
    }
}
