<?php

declare(strict_types=1);

use StackPay\Payments\Structures;

final class VoidTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\VoidTransaction::class;

    public function test_type() {
        $struct = new $this->struct;
        $this->assertEquals("Void", $struct->type() );
    }

    public function test_merchant() { $this->full( "merchant", Structures\Merchant::class, true); }
    public function test_id() { $this->full( "id", "int", false); }
    public function test_externalID() { $this->full( "externalID", "string", false); }
    public function test_paymentMethod() { $this->full( "paymentMethod", Structures\PaymentMethod::class, true); }
    public function test_customer() { $this->full( "customer", Structures\Customer::class, true ); }
    public function test_status() { $this->full( "status", "int", false); }
    public function test_order() { $this->full( "order", Structures\Order::class, true); }
    public function test_voidedTransaction() { $this->full( "voidedTransaction", Structures\Transaction::class, true); }
    public function test_originalTransaction() { $this->full( "originalTransaction", Structures\Transaction::class, true); }
}
