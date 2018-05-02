<?php

declare(strict_types=1);

use StackPay\Payments\Structures;

final class TransactionTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Transaction::class;

    public function test_type() {
        $struct = new $this->struct;
        $this->assertEquals("Undefined", $struct->type() );
    }

    public function test_id() { $this->full( "id", "int", false); }
    public function test_order() { $this->full( "order", Structures\Order::class, true); }
    public function test_customer() { $this->full( "customer", Structures\Customer::class, true ); }
    public function test_merchant() { $this->full( "merchant", Structures\Merchant::class, true); }
    public function test_paymentMethod() { $this->full( "paymentMethod", Structures\PaymentMethod::class, true); }
    public function test_status() { $this->full( "status", "int", false); }
    public function test_authCode() { $this->full( "authCode", "string", false); }
    public function test_avsCode() { $this->full( "avsCode", "string", false); }
    public function test_cvvResponseCode() { $this->full( "cvvResponseCode", "string", false); }
    public function test_currency() { $this->full( "currency", "string", false); }
    public function test_split()   { $this->full( "split", Structures\Split::class, true ); }
    public function test_amount() { $this->full( "amount", "int" ); }
    public function test_externalID() { $this->full( "externalID", "string", false); }
}
