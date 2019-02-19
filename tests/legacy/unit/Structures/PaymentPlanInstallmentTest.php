<?php

use StackPay\Payments\Structures;

final class PaymentPlanInstallmentTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\PaymentPlanInstallment::class;

    public function test_date()
    {
        $this->full('date', \DateTime::class, false);
    }

    public function test_percentage()
    {
        $this->full('percentage', 'int', false);
    }

    public function test_interval()
    {
        $this->full('interval', 'int', false);
    }
}
