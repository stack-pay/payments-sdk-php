<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class MerchantLinkTest extends TestCase
{
    public function testSuccessfulCase()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"be0482fdcd6cfd818ba96fbb9286db941794fc94f8b00b40403d34948418ad27"}},"Body":{"Status":1,"URL":"http:\/\/localhost:8080\/merchant\/application\/40e334f62c19dcdddeba516d7049c130e4a66a46e73b35c89b08497df7e325de"}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
            '7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
        );

        $sdk->setCurlProvider($curlProvider);

        $merchant = $sdk->generateMerchantLink(
            (new structures\merchant())
                ->setRate(
                    (new structures\Rate())->setName('Rate A')
                )
                ->setExternalID('93hfon39f8')
                ->setApplicationName('Test Application')
        );

        $this->assertEquals('http://localhost:8080/merchant/application/40e334f62c19dcdddeba516d7049c130e4a66a46e73b35c89b08497df7e325de', $merchant->link);

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/merchants/link',
                    'Body' => [
                        'Body'   => [
                            'ExternalId'        => '93hfon39f8',
                            'RateName'          => 'Rate A',
                            'ApplicationName'   => 'Test Application',
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => 'db39324c2ee45ee022de0cf18bb3790555e652e9309bc6e8d0d187a6b89259ef'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => 'db39324c2ee45ee022de0cf18bb3790555e652e9309bc6e8d0d187a6b89259ef'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }
}
