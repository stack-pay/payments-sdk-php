<?php

use StackPay\Payments\Structures;

final class RefundTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Refund::class;

    public function test_type()
    {
        $struct = new $this->struct;

        $this->assertEquals('Refund', $struct->type());
    }

    public function test_merchant()
    {
        $this->full('merchant', Structures\Merchant::class, true);
    }

    public function test_id()
    {
        $this->full('id', 'int', false);
    }

    public function test_invoiceNumber()
    {
        $this->full('invoiceNumber', 'string', false);
    }

    public function test_externalID()
    {
        $this->full('externalID', 'string', false);
    }

    public function test_comment1()
    {
        $this->full('comment1', 'string', false);
    }

    public function test_comment2()
    {
        $this->full('comment2', 'string', false);
    }

    public function test_currency()
    {
        $this->full('currency', 'string', false);
    }

    public function test_amount()
    {
        $this->full('amount', 'int');
    }

    public function test_paymentMethod()
    {
        $this->full('paymentMethod', Structures\PaymentMethod::class, true);
    }

    public function test_customer()
    {
        $this->full('customer', Structures\Customer::class, true);
    }

    public function test_status()
    {
        $this->full('status', 'int', false);
    }

    public function test_order()
    {
        $this->full('order', Structures\Order::class, true);
    }

    public function test_authCode()
    {
        $this->full('authCode', 'string', false);
    }

    public function test_avsCode()
    {
        $this->full('avsCode', 'string', false);
    }

    public function test_cvvResponseCode()
    {
        $this->full('cvvResponseCode', 'string', false);
    }

    public function test_split()
    {
        $this->full('split', Structures\Split::class, true);
    }

    public function test_refundedTransaction()
    {
        $this->full('refundedTransaction', Structures\Transaction::class, true);
    }

    public function test_originalTransaction()
    {
        $this->full('originalTransaction', Structures\Transaction::class, true);
    }
}
