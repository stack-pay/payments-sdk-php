<?php

use StackPay\Payments\Structures;

final class PaymentPlanConfigTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\PaymentPlanConfig::class;

    public function test_months()
    {
        $this->full('months', 'int', false);
    }

    public function test_day()
    {
        $this->full('day', 'int', false);
    }

    public function test_gracePeriod()
    {
        $this->full('gracePeriod', 'int', false);
    }

    public function test_installments()
    {
        $this->full('installments', 'array', false);
    }
}
