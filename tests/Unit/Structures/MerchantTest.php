<?php

declare(strict_types=1);

use StackPay\Payments\Structures;

final class MerchantTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Merchant::class;

    public function test_id() { $this->full( "id", "int", false); }
    public function test_hashKey() { $this->full( "hashKey", "string", false); }
    public function test_creditCardTransactionLimit() { $this->full( "creditCardTransactionLimit", "string", false); }
    public function test_creditCardMonthlyLimit() { $this->full( "creditCardMonthlyLimit", "string", false); }
    public function test_creditCardCurrentVolume() { $this->full( "creditCardCurrentVolume", "string", false); }
    public function test_achTransactionLimit() { $this->full( "achTransactionLimit", "string", false); }
    public function test_achMonthlyLimit() { $this->full( "achMonthlyLimit", "string", false); }
    public function test_achCurrentVolume() { $this->full( "achCurrentVolume", "string", false); }
    public function test_rates() { $this->full( "rates", "string", false); }

    public function test_rate() { $this->full( "rate", Structures\Rate::class, false); }
    public function test_link() { $this->full( "link", "string", false); }
    public function test_externalID() { $this->full( "externalID", "string", false); }

}
