<?php

declare(strict_types=1);

use StackPay\Payments\Structures;

final class AccountTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Account::class;

    public function test_savePaymentMethod() {
        $this->getter( "savePaymentMethod", "bool");
        $this->setter_notNull( "savePaymentMethod", "bool");
        $this->setter_false( "savePaymentMethod", "bool");
    }
    public function test_type() { $this->full( "type", "string", false); }
    public function test_number() { $this->full( "number", "int", false); }
    public function test_expireDate() { $this->full( "expireDate", "string", false); }
    public function test_expireMonth() { $this->full( "expireMonth", "string", false); }
    public function test_expireYear() { $this->full( "expireYear", "string", false); }
    public function test_cvv2() { $this->full( "cvv2", "int", false); }
    public function test_routingNumber() { $this->full( "routingNumber", "int", false); }
    public function test_last4() { $this->full( "last4", "int", false); }
    public function test_routingLast4() { $this->full( "routingLast4", "string", false); }

    // SavePaymentMethod
    // Type
    // Number
    // Expire Date
    // CVV2
    // Routing Number
    // Account Last 4
}
