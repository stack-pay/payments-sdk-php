<?php

use StackPay\Payments\Structures;

final class SubscriptionTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Subscription::class;

    public function test_id()
    {
        $this->full('id', 'int', false);
    }

    public function test_paymentMethod()
    {
        $this->full('paymentMethod', Structures\PaymentMethod::class, false);
    }

    public function test_paymentPlan()
    {
        $this->full('paymentPlan', Structures\PaymentPlan::class, false);
    }

    public function test_externalID()
    {
        $this->full('externalID', 'string', false);
    }

    public function test_amount()
    {
        $this->full('amount', 'int', false);
    }

    public function test_splitAmount()
    {
        $this->full('splitAmount', 'int', false);
    }

    public function test_initialTransaction()
    {
        $this->full('initialTransaction', Structures\Transaction::class, false);
    }

    public function test_scheduledTransactions()
    {
        $this->full('scheduledTransactions', 'array', false);
    }

}
