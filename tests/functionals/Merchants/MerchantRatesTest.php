<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures\Rate;

use Test\Mocks\Providers\MockCurlProvider;

final class MerchantRatesTest extends TestCase
{
    public function testSucessfulCase()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"4ee470f83a647c888554c1a7c57d4f828ef21176d538feff205371d39abc31ad"}},"Body":{"Status":1,"Rates":{"Rate A":{"fee_rate":3.65,"fee_transaction":0.3,"fee_notes":""},"Rate B":{"fee_rate":3.29,"fee_transaction":0.3,"fee_notes":""},"Rate C":{"fee_rate":2.9,"fee_transaction":0.3,"fee_notes":""},"Rate D":{"fee_rate":2.49,"fee_transaction":0.3,"fee_notes":"AMEX rate is 2.90%"}}}}'
                ,
                'Headers' => ['Header1' => 'test']
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $rates = $sdk->merchantRates()->rates;

        $this->assertCount(4,                       $rates);

        $this->assertEquals(3.65,                   $rates[0]->feeRate());
        $this->assertEquals(0.3,                    $rates[0]->feeTransaction());
        $this->assertEquals('',                     $rates[0]->feeNotes());
        $this->assertEquals('Rate A',               $rates[0]->name());

        $this->assertEquals(3.29,                   $rates[1]->feeRate());
        $this->assertEquals(0.3,                    $rates[1]->feeTransaction());
        $this->assertEquals('',                     $rates[1]->feeNotes());
        $this->assertEquals('Rate B',               $rates[1]->name());

        $this->assertEquals(2.9,                    $rates[2]->feeRate());
        $this->assertEquals(0.3,                    $rates[2]->feeTransaction());
        $this->assertEquals('',                     $rates[2]->feeNotes());
        $this->assertEquals('Rate C',               $rates[2]->name());

        $this->assertEquals(2.49,                   $rates[3]->feeRate());
        $this->assertEquals(0.3,                    $rates[3]->feeTransaction());
        $this->assertEquals('AMEX rate is 2.90%',   $rates[3]->feeNotes());
        $this->assertEquals('Rate D',               $rates[3]->name());

        $this->assertCount(1,                       $curlProvider->calls);

        $this->assertEquals([
        0 => [
            'URL'  => 'https://api.mystackpay.com/api/merchants/rates',
            'Body' => [
                'Body'    => null,
                'Header' => [
                    'Application' => 'PaymentSystem',
                    'ApiVersion'  => 'v1',
                    'Mode'        => 'production',
                    'Security'    => [
                        'HashMethod' => 'SHA-256',
                        'Hash'       => '09ad3aef2def29ff0ff353a299e74987fdf0f19f43cf9ca66077ed3eca3c326e'
                    ]
                ]
            ],
            'Headers' => [
                0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                2 => ['Key' => 'Mode',          'Value' => 'production'],
                3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                4 => ['Key' => 'Hash',          'Value' => '09ad3aef2def29ff0ff353a299e74987fdf0f19f43cf9ca66077ed3eca3c326e'],
                5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
            ]
        ]
        ], $curlProvider->calls );
    }
}
