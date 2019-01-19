<?php

use StackPay\Payments\Structures;

final class PaymentPlanTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\PaymentPlan::class;

    public function test_id()
    {
        $this->full('id', 'int', false);
    }

    public function test_name()
    {
        $this->full('name', 'string', false);
    }

    public function test_requestIncomingId()
    {
        $this->full('requestIncomingId', 'int', false);
    }

    public function test_downPaymentAmount()
    {
        $this->full('downPaymentAmount', 'int', false);
    }

    public function test_splitMerchant()
    {
        $this->full('splitMerchant', Structures\Merchant::class, false);
    }

    public function test_merchant()
    {
        $this->full('merchant', Structures\Merchant::class, false);
    }

    public function test_configuration()
    {
        $this->full('configuration', Structures\PaymentPlanConfig::class, false);
    }

    public function test_paymentPriority()
    {
        $this->full('paymentPriority', 'string', false);
    }
}
