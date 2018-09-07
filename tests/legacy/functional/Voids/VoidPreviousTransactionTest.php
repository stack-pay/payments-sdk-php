<?php

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\AccountTypes;
use StackPay\Payments\Currency;
use StackPay\Payments\Exceptions;
use StackPay\Payments\Factories;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class VoidPreviousTransactionTest extends TestCase
{
    public function testVoidAsRefund()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"3613f8bfa7822868b2d64475faa326292e491cd149bd176ea354dd3cbeea0881"}},"Body":{"Status":1,"Merchant":50,"Order":67,"Transaction":112,"Refund":{"Customer":64,"PaymentMethod":63,"Amount":10000,"Currency":"USD","SplitMerchant":2,"SplitAmount":1000,"RefundedTransaction":111}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
            '7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
        );

        $sdk->setCurlProvider($curlProvider);

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $capture = (new Structures\Capture())
            ->setID(111)
            ->setAmount(10000)
            ->setSplit($split);

        $merchant = (new Structures\Merchant())
            ->setID(50)
            ->setHashKey('aeb108184c6adc8a1f16ffd116700ef2b2f77ba0d43abbb42fe7e138d817918d');

        $void = $sdk->voidWithOriginalTransaction(
            $capture,
            $merchant
        );

        $this->assertEquals(
            [
                "ID"       => 112,
                "Status"   => 1,
                "Merchant" => [
                    "ID" => 50,
                ],
                "Order" => [
                    "ID" => 67,
                ],
                "Customer" => [
                    "ID" => 64,
                ],
                "Payment Method" => [
                    "ID" => 63,
                ],
                "Refunded Transaction" => [
                    "ID" => 111,
                ]
            ],
            [
                "ID"     => $void->id(),
                "Status" => $void->status(),
                "Merchant" => [
                    "ID" => $void->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $void->order()->id(),
                ],
                "Customer" => [
                    "ID" => $void->customer()->id(),
                ],
                "Payment Method"     => [
                    "ID" => $void->paymentMethod()->id(),
                ],
                "Refunded Transaction" => [
                    "ID" => $void->refundedTransaction()->id(),
                ]
            ]
        );

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/payments',
                    'Body' => [
                        'Body' => [
                            'Merchant' => 50,
                            'Order' => [
                                'Transaction' => [
                                    'Type'      => 'Void',
                                    'Comment1'  => null,
                                    'Comment2'  => null,
                                ],
                                'OriginalTransaction' => 111
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => 'fadb80260b75db1efd32f4b321a269a51bd5cfe2c6abb37562f736ff1e5c6ab7'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => 'fadb80260b75db1efd32f4b321a269a51bd5cfe2c6abb37562f736ff1e5c6ab7'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testVoidAsRefundWithFactory()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"3613f8bfa7822868b2d64475faa326292e491cd149bd176ea354dd3cbeea0881"}},"Body":{"Status":1,"Merchant":50,"Order":67,"Transaction":112,"Refund":{"Customer":64,"PaymentMethod":63,"Amount":10000,"Currency":"USD","SplitMerchant":2,"SplitAmount":1000,"RefundedTransaction":111}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
            '7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
        );

        $sdk->setCurlProvider($curlProvider);

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $merchant = (new Structures\Merchant())
            ->setID(50)
            ->setHashKey('aeb108184c6adc8a1f16ffd116700ef2b2f77ba0d43abbb42fe7e138d817918d');

        $capture = (new Structures\Capture())
            ->setID(111)
            ->setAmount(10000)
            ->setSplit($split)
            ->setMerchant($merchant);

        $transaction = Factories\VoidTransaction::previousTransaction(
            $capture
        );

        $void = $sdk->processTransaction($transaction);

        $this->assertEquals(
            [
                "ID"       => 112,
                "Status"   => 1,
                "Merchant" => [
                    "ID" => 50,
                ],
                "Order" => [
                    "ID" => 67,
                ],
                "Customer" => [
                    "ID" => 64,
                ],
                "Payment Method" => [
                    "ID" => 63,
                ],
                "Refunded Transaction" => [
                    "ID" => 111,
                ]
            ],
            [
                "ID"     => $void->id(),
                "Status" => $void->status(),
                "Merchant" => [
                    "ID" => $void->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $void->order()->id(),
                ],
                "Customer" => [
                    "ID" => $void->customer()->id(),
                ],
                "Payment Method"     => [
                    "ID" => $void->paymentMethod()->id(),
                ],
                "Refunded Transaction" => [
                    "ID" => $void->refundedTransaction()->id(),
                ]
            ]
        );

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/payments',
                    'Body' => [
                        'Body' => [
                            'Merchant' => 50,
                            'Order' => [
                                'Transaction' => [
                                    'Type'      => 'Void',
                                    'Comment1'  => null,
                                    'Comment2'  => null,
                                ],
                                'OriginalTransaction' => 111
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => 'fadb80260b75db1efd32f4b321a269a51bd5cfe2c6abb37562f736ff1e5c6ab7'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => 'fadb80260b75db1efd32f4b321a269a51bd5cfe2c6abb37562f736ff1e5c6ab7'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testVoidAuth()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"5d7d609d6157d6f1f3c6a63a7ca4129c3a36d46013790524478f98e1ef321bdb"}},"Body":{"Status":1,"Merchant":50,"Order":66,"Transaction":109,"Void":{"Customer":63,"PaymentMethod":62,"VoidedTransaction":108}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
            '7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
        );

        $sdk->setCurlProvider($curlProvider);

        $auth = (new Structures\Auth())
            ->setID(108);

        $merchant = (new Structures\Merchant())
            ->setID(50)
            ->setHashKey('aeb108184c6adc8a1f16ffd116700ef2b2f77ba0d43abbb42fe7e138d817918d');

        $void = $sdk->voidWithOriginalTransaction(
            $auth,
            $merchant
        );

        $this->assertEquals(
            [
                "ID"       => 109,
                "Status"   => 1,
                "Merchant" => [
                    "ID" => 50,
                ],
                "Order" => [
                    "ID" => 66,
                ],
                "Customer" => [
                    "ID" => 63,
                ],
                "Payment Method" => [
                    "ID" => 62,
                ],
                "Voided Transaction" => [
                    "ID" => 108,
                ]
            ],
            [
                "ID"     => $void->id(),
                "Status" => $void->status(),
                "Merchant" => [
                    "ID" => $void->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $void->order()->id(),
                ],
                "Customer" => [
                    "ID" => $void->customer()->id(),
                ],
                "Payment Method"     => [
                    "ID" => $void->paymentMethod()->id(),
                ],
                "Voided Transaction" => [
                    "ID" => $void->voidedTransaction()->id(),
                ]
            ]
        );

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/payments',
                    'Body' => [
                        'Body' => [
                            'Merchant' => 50,
                            'Order' => [
                                'Transaction' => [
                                    'Type'      => 'Void',
                                    'Comment1'  => null,
                                    'Comment2'  => null,
                                ],
                                'OriginalTransaction' => 108
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => 'c5f88d7472fc17f8761962525d2c4910ca667d84ed1ab78859cb30115c777855'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => 'c5f88d7472fc17f8761962525d2c4910ca667d84ed1ab78859cb30115c777855'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testVoidCapture()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"d0c737eaa4389d4aa410aafa6e89b8b55d1c9a31af58c9abafe2d879fe3674ef"}},"Body":{"Status":1,"Merchant":50,"Order":67,"Transaction":112,"Void":{"Customer":64,"PaymentMethod":63,"VoidedTransaction":111}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
            '7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
        );

        $sdk->setCurlProvider($curlProvider);

        $capture = (new Structures\Capture())
            ->setID(111);

        $merchant = (new Structures\Merchant())
            ->setID(50)
            ->setHashKey('aeb108184c6adc8a1f16ffd116700ef2b2f77ba0d43abbb42fe7e138d817918d');

        $void = $sdk->voidWithOriginalTransaction(
            $capture,
            $merchant
        );

        $this->assertEquals(
            [
                "ID"       => 112,
                "Status"   => 1,
                "Merchant" => [
                    "ID" => 50,
                ],
                "Order" => [
                    "ID" => 67,
                ],
                "Customer" => [
                    "ID" => 64,
                ],
                "Payment Method" => [
                    "ID" => 63,
                ],
                "Voided Transaction" => [
                    "ID" => 111,
                ]
            ],
            [
                "ID"     => $void->id(),
                "Status" => $void->status(),
                "Merchant" => [
                    "ID" => $void->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $void->order()->id(),
                ],
                "Customer" => [
                    "ID" => $void->customer()->id(),
                ],
                "Payment Method"     => [
                    "ID" => $void->paymentMethod()->id(),
                ],
                "Voided Transaction" => [
                    "ID" => $void->voidedTransaction()->id(),
                ]
            ]
        );

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/payments',
                    'Body' => [
                        'Body' => [
                            'Merchant' => 50,
                            'Order' => [
                                'Transaction' => [
                                    'Type'      => 'Void',
                                    'Comment1'  => null,
                                    'Comment2'  => null,
                                ],
                                'OriginalTransaction' => 111
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => 'fadb80260b75db1efd32f4b321a269a51bd5cfe2c6abb37562f736ff1e5c6ab7'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => 'fadb80260b75db1efd32f4b321a269a51bd5cfe2c6abb37562f736ff1e5c6ab7'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testVoidRefund()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"af49d9973ee58cf687bea10c4e18c9916e238df4463662ad47c53bc970c98cf6"}},"Body":{"Status":1,"Merchant":50,"Order":79,"Transaction":147,"Void":{"Customer":76,"PaymentMethod":75,"VoidedTransaction":146}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
            '7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
        );

        $sdk->setCurlProvider($curlProvider);

        $refund = (new Structures\Refund())
            ->setID(146);

        $merchant = (new Structures\Merchant())
            ->setID(50)
            ->setHashKey('aeb108184c6adc8a1f16ffd116700ef2b2f77ba0d43abbb42fe7e138d817918d');

        $void = $sdk->voidWithOriginalTransaction(
            $refund,
            $merchant
        );

        $this->assertEquals(
            [
                "ID"       => 147,
                "Status"   => 1,
                "Merchant" => [
                    "ID" => 50,
                ],
                "Order" => [
                    "ID" => 79,
                ],
                "Customer" => [
                    "ID" => 76,
                ],
                "Payment Method" => [
                    "ID" => 75,
                ],
                "Voided Transaction" => [
                    "ID" => 146,
                ]
            ],
            [
                "ID"     => $void->id(),
                "Status" => $void->status(),
                "Merchant" => [
                    "ID" => $void->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $void->order()->id(),
                ],
                "Customer" => [
                    "ID" => $void->customer()->id(),
                ],
                "Payment Method"     => [
                    "ID" => $void->paymentMethod()->id(),
                ],
                "Voided Transaction" => [
                    "ID" => $void->voidedTransaction()->id(),
                ]
            ]
        );

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/payments',
                    'Body' => [
                        'Body' => [
                            'Merchant' => 50,
                            'Order' => [
                                'Transaction' => [
                                    'Type'      => 'Void',
                                    'Comment1'  => null,
                                    'Comment2'  => null,
                                ],
                                'OriginalTransaction' => 146
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '8e6de64bc52853493e450f4b55f5936b384563c3c8ac1277a43e9750fd36414e'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '8e6de64bc52853493e450f4b55f5936b384563c3c8ac1277a43e9750fd36414e'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testVoidSale()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"30028045cdbb7cab3866233a11428bebe596b2f6512aa89cf233ef6ebd13e4e8"}},"Body":{"Status":1,"Merchant":5,"Order":570,"Transaction":749,"Void":{"Customer":537,"PaymentMethod":529,"VoidedTransaction":748}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $sale = (new Structures\Sale())
            ->setID(748);

        $merchant = (new Structures\Merchant())
            ->setID(5)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $void = $sdk->voidWithOriginalTransaction(
            $sale,
            $merchant
        );

        $this->assertEquals(5, $void->merchant()->id);
        $this->assertEquals(529, $void->paymentMethod()->id);
        $this->assertEquals(570, $void->order()->id);
        $this->assertEquals(749, $void->id);
        $this->assertEquals(537, $void->customer()->id);
        $this->assertEquals(748, $void->voidedTransaction()->id);

        $this->assertCount(1, $curlProvider->calls );

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/payments',
                    'Body' => [
                        'Body' => [
                            'Merchant' => 5,
                            'Order' => [
                                'Transaction' => [
                                    'Type'      => 'Void',
                                    'Comment1'  => null,
                                    'Comment2'  => null,
                                ],
                                'OriginalTransaction' => 748
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => 'ed159acbf9422684a1f2808a6b7517a459fc5492d76cb53686505d1cf6f3255d'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => 'ed159acbf9422684a1f2808a6b7517a459fc5492d76cb53686505d1cf6f3255d'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }
}
