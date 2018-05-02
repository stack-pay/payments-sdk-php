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

final class RefundWithOriginalTransactionTest extends TestCase
{
    public function testRefundAsVoid()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"738c9e3ae25e8e6047707558dca193e2e9b0723bd05a33e4c6cf19e441eb3382"}},"Body":{"Status":1,"Merchant":4,"Order":569,"Transaction":747,"Void":{"Customer":1,"PaymentMethod":528,"VoidedTransaction":746}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $capture = (new Structures\Capture())
            ->setID(746);

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(10)
            ->setMerchant($splitMerchant);

        $refund = $sdk->refundWithOriginalTransaction(
            $capture,
            50,
            $split,
            $merchant
        );

        $this->assertEquals([
            "ID"       => 747,
            "Status"   => 1,
            "Amount"   => 50,
            "Currency" => null,
            "Voided Transaction" => [
                "ID" => 746,
            ],
            "Merchant" => [
                "ID" => 4,
            ],
            "Order" => [
                "ID" => 569,
            ],
            "Customer" => [
                "ID" => 1,
            ],
            "Split" => [
                "Merchant" => [
                    "ID" => 2,
                ],
                "Amount" => 10,
            ],
            "Payment Method" => [
                "ID" => 528,
            ],
        ],[
            "ID"                 => $refund->id(),
            "Status"             => $refund->status(),
            "Amount"             => $refund->amount(),
            "Currency"           => $refund->currency(),
            "Voided Transaction" => [
                "ID" => $refund->voidedTransaction()->id(),
            ],
            "Merchant" => [
                "ID" => $refund->merchant()->id(),
            ],
            "Order" => [
                "ID" => $refund->order()->id(),
            ],
            "Customer" => [
                "ID" => $refund->customer()->id(),
            ],
            "Split" => [
                "Merchant" => [
                    "ID" => $refund->split()->merchant()->id(),
                ],
                "Amount" => $refund->split()->amount(),
            ],
            "Payment Method"     => [
                "ID" => $refund->paymentMethod()->id(),
            ],
        ]);

        $this->assertCount(1,       $curlProvider->calls);

        $this->assertEquals([
            0 => [
                'URL'  => 'https://api.mystackpay.com/api/payments',
                'Body' => [
                    'Body' => [
                        'Merchant' => 4,
                        'Order' => [
                            'Transaction' => [
                                'Type'          => 'Refund',
                                'Amount'        => 50,
                                'SplitAmount'   => 10,
                                'SplitMerchant' => 2
                            ],
                            'OriginalTransaction' => 746
                        ]
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => '1.0.0',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => 'e94dbbbda80137c00ca0895b8f1799babde0cdd79d7ed33d2e651fac9dbb8738'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => '1.0.0'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => 'e94dbbbda80137c00ca0895b8f1799babde0cdd79d7ed33d2e651fac9dbb8738'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
        ], $curlProvider->calls );
    }

    public function testRefundCapture()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"448faec95777c63c895601912c7f255156c0025bf3ee48c5e0d0c66fe84b5831"}},"Body":{"Status":1,"Merchant":4,"Order":569,"Transaction":747,"Refund":{"Customer":1,"PaymentMethod":528,"RefundedTransaction":746,"Amount":50,"SplitMerchant":2,"SplitAmount":10,"Currency":"USD"}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $capture = (new Structures\Capture())
            ->setID(746);

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(10)
            ->setMerchant($splitMerchant);

        $refund = $sdk->refundWithOriginalTransaction(
            $capture,
            50,
            $split,
            $merchant
        );

        $this->assertEquals([
            "ID"       => 747,
            "Status"   => 1,
            "Amount"   => 50,
            "Currency" => Currency::USD,
            "Refunded Transaction" => [
                "ID" => 746,
            ],
            "Merchant" => [
                "ID" => 4,
            ],
            "Order" => [
                "ID" => 569,
            ],
            "Customer" => [
                "ID" => 1,
            ],
            "Split" => [
                "Merchant" => [
                    "ID" => 2,
                ],
                "Amount" => 10,
            ],
            "Payment Method" => [
                "ID" => 528,
            ],
        ],[
            "ID"                 => $refund->id(),
            "Status"             => $refund->status(),
            "Amount"             => $refund->amount(),
            "Currency"           => $refund->currency(),
            "Refunded Transaction" => [
                "ID" => $refund->refundedTransaction()->id(),
            ],
            "Merchant" => [
                "ID" => $refund->merchant()->id(),
            ],
            "Order" => [
                "ID" => $refund->order()->id(),
            ],
            "Customer" => [
                "ID" => $refund->customer()->id(),
            ],
            "Split" => [
                "Merchant" => [
                    "ID" => $refund->split()->merchant()->id(),
                ],
                "Amount" => $refund->split()->amount(),
            ],
            "Payment Method"     => [
                "ID" => $refund->paymentMethod()->id(),
            ],
        ]);

        $this->assertCount(1,       $curlProvider->calls);

        $this->assertEquals([
            0 => [
                'URL'  => 'https://api.mystackpay.com/api/payments',
                'Body' => [
                    'Body' => [
                        'Merchant' => 4,
                        'Order' => [
                            'Transaction' => [
                                'Type'          => 'Refund',
                                'Amount'        => 50,
                                'SplitAmount'   => 10,
                                'SplitMerchant' => 2
                            ],
                            'OriginalTransaction' => 746
                        ]
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => '1.0.0',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => 'e94dbbbda80137c00ca0895b8f1799babde0cdd79d7ed33d2e651fac9dbb8738'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => '1.0.0'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => 'e94dbbbda80137c00ca0895b8f1799babde0cdd79d7ed33d2e651fac9dbb8738'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
        ], $curlProvider->calls );
    }

    public function testRefundSale()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"617bbf7341e48937912ecdf5b23bf926f4e3574785e21f1f44d5ace1977b5836"}},"Body":{"Status":1,"Merchant":5,"Order":564,"Transaction":737,"Refund":{"Customer":536,"PaymentMethod":523,"RefundedTransaction":736,"Amount":90,"SplitMerchant":2,"SplitAmount":20,"Currency":"USD"}}}'
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
            ->setID(736);

        $merchant = (new Structures\Merchant())
            ->setID(5)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(20)
            ->setMerchant($splitMerchant);

        $refund = $sdk->refundWithOriginalTransaction(
            $sale,
            90,
            $split,
            $merchant
        );

        $this->assertEquals(5,      $refund->merchant()->id);
        $this->assertEquals(523,    $refund->paymentMethod()->id);
        $this->assertEquals(564,    $refund->order()->id);
        $this->assertEquals(737,    $refund->id);
        $this->assertEquals(536,    $refund->customer()->id);
        $this->assertEquals(736,    $refund->refundedTransaction()->id);

        $this->assertCount(1,       $curlProvider->calls);

        $this->assertEquals([
            0 => [
                'URL'  => 'https://api.mystackpay.com/api/payments',
                'Body' => [
                    'Body' => [
                        'Merchant' => 5,
                        'Order' => [
                            'Transaction' => [
                                'Type'          => 'Refund',
                                'Amount'        => 90,
                                'SplitAmount'   => 20,
                                'SplitMerchant' => 2
                            ],
                            'OriginalTransaction' => 736
                        ]
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => '1.0.0',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => '2bc647fbb895a08d41dfff9d944be3b17472f328e495ffc8677f16d423482280'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => '1.0.0'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => '2bc647fbb895a08d41dfff9d944be3b17472f328e495ffc8677f16d423482280'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
        ], $curlProvider->calls );
    }

    public function testRefundWithFactory()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"738c9e3ae25e8e6047707558dca193e2e9b0723bd05a33e4c6cf19e441eb3382"}},"Body":{"Status":1,"Merchant":4,"Order":569,"Transaction":747,"Void":{"Customer":1,"PaymentMethod":528,"VoidedTransaction":746}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $capture = (new Structures\Capture())
            ->setID(746);

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(10)
            ->setMerchant($splitMerchant);

        $transaction = \StackPay\Payments\Factories\Refund::previousTransaction(
            $capture,
            50,
            $merchant,
            $split
        );

        $refund = $sdk->processTransaction($transaction);

        $this->assertEquals([
            "ID"       => 747,
            "Status"   => 1,
            "Amount"   => 50,
            "Currency" => 'USD',
            "Voided Transaction" => [
                "ID" => 746,
            ],
            "Merchant" => [
                "ID" => 4,
            ],
            "Order" => [
                "ID" => 569,
            ],
            "Customer" => [
                "ID" => 1,
            ],
            "Split" => [
                "Merchant" => [
                    "ID" => 2,
                ],
                "Amount" => 10,
            ],
            "Payment Method" => [
                "ID" => 528,
            ],
        ],[
            "ID"                 => $refund->id(),
            "Status"             => $refund->status(),
            "Amount"             => $refund->amount(),
            "Currency"           => $refund->currency(),
            "Voided Transaction" => [
                "ID" => $refund->voidedTransaction()->id(),
            ],
            "Merchant" => [
                "ID" => $refund->merchant()->id(),
            ],
            "Order" => [
                "ID" => $refund->order()->id(),
            ],
            "Customer" => [
                "ID" => $refund->customer()->id(),
            ],
            "Split" => [
                "Merchant" => [
                    "ID" => $refund->split()->merchant()->id(),
                ],
                "Amount" => $refund->split()->amount(),
            ],
            "Payment Method"     => [
                "ID" => $refund->paymentMethod()->id(),
            ],
        ]);

        $this->assertCount(1,       $curlProvider->calls);

        $this->assertEquals([
            0 => [
                'URL'  => 'https://api.mystackpay.com/api/payments',
                'Body' => [
                    'Body' => [
                        'Merchant' => 4,
                        'Order' => [
                            'Transaction' => [
                                'Type'          => 'Refund',
                                'Amount'        => 50,
                                'SplitAmount'   => 10,
                                'SplitMerchant' => 2
                            ],
                            'OriginalTransaction' => 746
                        ]
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => '1.0.0',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => 'e94dbbbda80137c00ca0895b8f1799babde0cdd79d7ed33d2e651fac9dbb8738'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => '1.0.0'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => 'e94dbbbda80137c00ca0895b8f1799babde0cdd79d7ed33d2e651fac9dbb8738'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
        ], $curlProvider->calls );
    }
}
