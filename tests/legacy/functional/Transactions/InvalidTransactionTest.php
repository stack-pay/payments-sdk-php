<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\AccountTypes;
use StackPay\Payments\Currency;
use StackPay\Payments\Exceptions;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class InvalidTransactionTest extends TestCase
{
    public function testInvalidTransactionType()
    {
        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $auth = (new InvalidTransactionType());

        try {
            $sdk->processTransaction($auth);
        } catch (Exception $e) {
            $this->assertEquals('Unknown Payment type', $e->getMessage());
        }

    }

}

class InvalidTransactionType extends Structures\Auth
{
    public function type()
    {
        return 'Invalid';
    }
}
