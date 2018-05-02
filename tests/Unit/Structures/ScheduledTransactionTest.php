<?php

declare(strict_types=1);

use StackPay\Payments\Structures;

final class ScheduledTransactionTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\ScheduledTransaction::class;

    public function test_id() { $this->full('id', 'int', false); }
    public function test_customer() { $this->full('customer', Structures\Customer::class, true); }
    public function test_merchant() { $this->full('merchant', Structures\Merchant::class, true); }
    public function test_paymentMethod() { $this->full('paymentMethod', Structures\PaymentMethod::class, true); }
    public function test_amount() { $this->full('amount', 'int', false); }
    public function test_scheduledAt() { $this->full('scheduledAt', \DateTime::class, false); }
    public function test_currencyCode() { $this->full('currencyCode', 'string', false); }
    public function test_split() { $this->full('split', Structures\Split::class, true); }

}
