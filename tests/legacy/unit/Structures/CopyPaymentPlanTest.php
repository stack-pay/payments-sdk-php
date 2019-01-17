<?php

use StackPay\Payments\Structures;

final class CopyPaymentPlanTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\CopyPaymentPlan::class;

    public function test_paymentPlan()
    {
        $this->full('paymentPlan', Structures\PaymentPlan::class, false);
    }
    public function test_splitMerchant()
    {
        $this->full('splitMerchant', Structures\Merchant::class, false);
    }
    public function test_merchant()
    {
        $this->full('merchant', Structures\Merchant::class, false);
    }
    public function test_paymentPriority()
    {
        $this->full('paymentPriority', 'string', false);
    }
}
