<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class MerchantLimitsTest extends TestCase
{
    public function testSuccessfulCase()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"8a93b80538a891e65a285f91951c713e823c14b4d4a5be1715c1650a9bacf4c4"}},"Body":{"Status":1,"Merchant":4,"Limits":{"credit_card_transaction_limit":"200000","credit_card_monthly_limit":"2500000","credit_card_current_volume":"21567","ach_transaction_limit":"100000","ach_monthly_limit":"1800000","ach_current_volume":"0"}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $merchant = $sdk->merchantLimits(
            (new Structures\Merchant())
                ->setID(4)
                ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8')
        );

        $this->assertEquals('200000',   $merchant->creditCardTransactionLimit);
        $this->assertEquals('2500000',  $merchant->creditCardMonthlyLimit);
        $this->assertEquals('21567',    $merchant->creditCardCurrentVolume);

        $this->assertEquals('100000',   $merchant->achTransactionLimit);
        $this->assertEquals('1800000',  $merchant->achMonthlyLimit);
        $this->assertEquals('0',        $merchant->achCurrentVolume);

        $this->assertCount(1,           $curlProvider->calls);

        $this->assertEquals([
        0 => [
            'URL'  => 'https://api.mystackpay.com/api/merchants/limits',
            'Body' => [
                'Body'   => [
                    'Merchant' => 4
                ],
                'Header' => [
                    'Application' => 'PaymentSystem',
                    'ApiVersion'  => 'v1',
                    'Mode'        => 'production',
                    'Security'    => [
                        'HashMethod' => 'SHA-256',
                        'Hash'       => '8daf0137654f5558cbaa86bcf3977e4e1e8f00771a4ec243d5b2be4d22d7a0b9'
                    ]
                ]
            ],
            'Headers' => [
                0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                2 => ['Key' => 'Mode',          'Value' => 'production'],
                3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                4 => ['Key' => 'Hash',          'Value' => '8daf0137654f5558cbaa86bcf3977e4e1e8f00771a4ec243d5b2be4d22d7a0b9'],
                5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
            ]
        ]
        ], $curlProvider->calls );
    }
}
