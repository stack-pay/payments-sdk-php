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

final class SaleWithPaymentMethodTest extends TestCase
{
    public function testSuccessfulCase()
    {

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $merchantHash = 'f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8';

        $curlBody = [
            'Status'      => 1,
            'Merchant'    => 4,
            'Order'       => 559,
            'Transaction' => 728,
            'Payment'     => [
                'Customer'          => 3,
                'PaymentMethod'     => null,
                'Amount'            => 10000,
                'SplitMerchant'     => 2,
                'SplitAmount'       => 1000,
                'Currency'          => 'USD',
                'AuthorizationCode' => 'A11111',
                'AVSCode'           => 'T',
                'CVVResponseCode'   => 'NotPresent',
                'SoftDescriptor'    => 'BSPAY - Payment',
            ],
            'PaymentMethod' => [
                'ID'              => 3,
                'AccountType'     => 'amex',
                'AccountLast4'    => '4121',
                'ExpirationMonth' => '7',
                'ExpirationYear'  => '2027',
                'BillingAddress'  => [
                    'AddressLine1' => '69976 Mills Cliffs',
                    'AddressLine2' => 'Suite 479',
                    'City'         => 'Feestfort',
                    'State'        => 'VT',
                    'Zip'          => '04059-2412',
                    'Country'      => 'USA'
                ]
            ]
        ];

        $respArray = [
            'Header' => [
                'Security' => [
                    'HashMethod' => 'SHA-256',
                    'Hash'       => hash("sha256",json_encode($curlBody).$merchantHash)
                ]
            ],
            'Body' => $curlBody,
        ];

        $curlProvider = new MockCurlProvider([[
            'StatusCode' => 200,
            'Body'       => json_encode($respArray),
            'Headers'    => []
        ]]);

        $sdk->setCurlProvider($curlProvider);

        $paymentMethod = (new Structures\PaymentMethod())
            ->setID(3);

        $merchant =(new Structures\Merchant())
            ->setID(4)
            ->setHashKey($merchantHash);

        $splitMerchant =(new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $sale = $sdk->saleWithPaymentMethod(
            $paymentMethod,
            $merchant,
            10000,        // Amount
            $split,
            null,         // Idempotency Key
            Currency::USD,
            'BSPAY - Payment'
        );

        $this->assertEquals(
            [
                "ID"                 => 728,
                "Status"             => 1,
                "Amount"             => 10000,
                "Currency"           => Currency::USD,
                "Authorization Code" => "A11111",
                "AVS Code"           => "T",
                "CVV Response Code"  => "NotPresent",
                "SoftDescriptor"     => "BSPAY - Payment",
                "Merchant" => [
                    "ID" => 4,
                ],
                "Order" => [
                    "ID" => 559,
                ],
                "Customer" => [
                    "ID" => 3,
                ],
                "Split" => [
                    "Merchant" => [
                        "ID" => 2,
                    ],
                    "Amount" => 1000,
                ],
                "Payment Method"     => [
                    "ID" => 3,
                    "Account" => [
                        "Type"             => AccountTypes::AMEX,
                        "Last 4"           => "4121",
                        "Expiration Month" => "7",
                        "Expiration Year"  => "2027",
                    ],
                    "Account Holder" => [
                        "Billing Address" => [
                            "Address 1" => "69976 Mills Cliffs",
                            "Address 2" => "Suite 479",
                            "City"      => "Feestfort",
                            "State"     => "VT",
                            "Postal Code" => "04059-2412",
                            "Country"     => "USA",
                        ],
                    ],
                ],
            ],
            [
                "ID"                 => $sale->id(),
                "Status"             => $sale->status(),
                "Amount"             => $sale->amount(),
                "Currency"           => $sale->currency(),
                "Authorization Code" => $sale->authCode(),
                "AVS Code"           => $sale->avsCode(),
                "CVV Response Code"  => $sale->cvvResponseCode(),
                "SoftDescriptor"     => $sale->softDescriptor(),
                "Merchant" => [
                    "ID" => $sale->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $sale->order()->id(),
                ],
                "Customer" => [
                    "ID" => $sale->customer()->id(),
                ],
                "Split" => [
                    "Merchant" => [
                        "ID" => $sale->split()->merchant()->id(),
                    ],
                    "Amount" => $sale->split()->amount(),
                ],
                "Payment Method"     => [
                    "ID" => $sale->paymentMethod()->id(),
                    "Account" => [
                        "Type"             => $sale->paymentMethod()->account()->type(),
                        "Last 4"           => $sale->paymentMethod()->account()->last4(),
                        "Expiration Month" => $sale->paymentMethod()->account()->expireMonth(),
                        "Expiration Year"  => $sale->paymentMethod()->account()->expireYear(),
                    ],
                    "Account Holder" => [
                        "Billing Address" => [
                            "Address 1" => $sale->paymentMethod()->accountHolder()->billingAddress()->address1(),
                            "Address 2" => $sale->paymentMethod()->accountHolder()->billingAddress()->address2(),
                            "City"      => $sale->paymentMethod()->accountHolder()->billingAddress()->city(),
                            "State"     => $sale->paymentMethod()->accountHolder()->billingAddress()->state(),
                            "Postal Code" => $sale->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
                            "Country"     => $sale->paymentMethod()->accountHolder()->billingAddress()->country(),
                        ],
                    ],
                ],
            ]
        );

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/payments',
                    'Body' => [
                        'Body' => [
                            'Merchant' => 4,
                            'Order' => [
                                'Transaction' => [
                                    'Type'           => 'Sale',
                                    'Currency'       => 'USD',
                                    'Amount'         => 10000,
                                    'InvoiceNumber'  => null,
                                    'ExternalId'     => null,
                                    'Comment1'       => null,
                                    'Comment2'       => null,
                                    'SoftDescriptor' => 'BSPAY - Payment',
                                    'SplitAmount'    => 1000,
                                    'SplitMerchant'  => 2,
                                ],
                                'PaymentMethod' => 3
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => hash("sha256",json_encode($curlProvider->calls[0]["Body"]["Body"]).$merchantHash)
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => hash("sha256",json_encode($curlProvider->calls[0]["Body"]["Body"]).$merchantHash)],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testWithFactory()
    {

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $merchantHash = 'f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8';

        $curlBody = [
            'Status'      => 1,
            'Merchant'    => 4,
            'Order'       => 559,
            'Transaction' => 728,
            'Payment'     => [
                'Customer'          => 3,
                'PaymentMethod'     => null,
                'Amount'            => 10000,
                'SplitMerchant'     => 2,
                'SplitAmount'       => 1000,
                'Currency'          => 'USD',
                'AuthorizationCode' => 'A11111',
                'AVSCode'           => 'T',
                'CVVResponseCode'   => 'NotPresent',
                'SoftDescriptor'    => 'BSPAY - Payment',
            ],
            'PaymentMethod' => [
                'ID'              => 3,
                'AccountType'     => 'amex',
                'AccountLast4'    => '4121',
                'ExpirationMonth' => '7',
                'ExpirationYear'  => '2027',
                'BillingAddress'  => [
                    'AddressLine1' => '69976 Mills Cliffs',
                    'AddressLine2' => 'Suite 479',
                    'City'         => 'Feestfort',
                    'State'        => 'VT',
                    'Zip'          => '04059-2412',
                    'Country'      => 'USA'
                ]
            ]
        ];

        $respArray = [
            'Header' => [
                'Security' => [
                    'HashMethod' => 'SHA-256',
                    'Hash'       => hash("sha256",json_encode($curlBody).$merchantHash)
                ]
            ],
            'Body' => $curlBody,
        ];

        $curlProvider = new MockCurlProvider([[
            'StatusCode' => 200,
            'Body'       => json_encode($respArray),
            'Headers'    => []
        ]]);

        $sdk->setCurlProvider($curlProvider);

        $paymentMethod = (new Structures\PaymentMethod())
            ->setID(3);

        $merchant =(new Structures\Merchant())
            ->setID(4)
            ->setHashKey($merchantHash);

        $splitMerchant =(new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = Factories\Sale::withPaymentMethod(
            $paymentMethod,
            $merchant,
            10000,        // Amount
            $split,
            Currency::USD,
            'BSPAY - Payment'
        );

        $sale = $sdk->processTransaction($transaction);

        $this->assertEquals(
            [
                "ID"                 => 728,
                "Status"             => 1,
                "Amount"             => 10000,
                "Currency"           => Currency::USD,
                "Authorization Code" => "A11111",
                "AVS Code"           => "T",
                "CVV Response Code"  => "NotPresent",
                "SoftDescriptor"     => "BSPAY - Payment",
                "Merchant" => [
                    "ID" => 4,
                ],
                "Order" => [
                    "ID" => 559,
                ],
                "Customer" => [
                    "ID" => 3,
                ],
                "Split" => [
                    "Merchant" => [
                        "ID" => 2,
                    ],
                    "Amount" => 1000,
                ],
                "Payment Method"     => [
                    "ID" => 3,
                    "Account" => [
                        "Type"             => AccountTypes::AMEX,
                        "Last 4"           => "4121",
                        "Expiration Month" => "7",
                        "Expiration Year"  => "2027",
                    ],
                    "Account Holder" => [
                        "Billing Address" => [
                            "Address 1" => "69976 Mills Cliffs",
                            "Address 2" => "Suite 479",
                            "City"      => "Feestfort",
                            "State"     => "VT",
                            "Postal Code" => "04059-2412",
                            "Country"     => "USA",
                        ],
                    ],
                ],
            ],
            [
                "ID"                 => $sale->id(),
                "Status"             => $sale->status(),
                "Amount"             => $sale->amount(),
                "Currency"           => $sale->currency(),
                "Authorization Code" => $sale->authCode(),
                "AVS Code"           => $sale->avsCode(),
                "CVV Response Code"  => $sale->cvvResponseCode(),
                "SoftDescriptor"     => $sale->softDescriptor(),
                "Merchant" => [
                    "ID" => $sale->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $sale->order()->id(),
                ],
                "Customer" => [
                    "ID" => $sale->customer()->id(),
                ],
                "Split" => [
                    "Merchant" => [
                        "ID" => $sale->split()->merchant()->id(),
                    ],
                    "Amount" => $sale->split()->amount(),
                ],
                "Payment Method"     => [
                    "ID" => $sale->paymentMethod()->id(),
                    "Account" => [
                        "Type"             => $sale->paymentMethod()->account()->type(),
                        "Last 4"           => $sale->paymentMethod()->account()->last4(),
                        "Expiration Month" => $sale->paymentMethod()->account()->expireMonth(),
                        "Expiration Year"  => $sale->paymentMethod()->account()->expireYear(),
                    ],
                    "Account Holder" => [
                        "Billing Address" => [
                            "Address 1" => $sale->paymentMethod()->accountHolder()->billingAddress()->address1(),
                            "Address 2" => $sale->paymentMethod()->accountHolder()->billingAddress()->address2(),
                            "City"      => $sale->paymentMethod()->accountHolder()->billingAddress()->city(),
                            "State"     => $sale->paymentMethod()->accountHolder()->billingAddress()->state(),
                            "Postal Code" => $sale->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
                            "Country"     => $sale->paymentMethod()->accountHolder()->billingAddress()->country(),
                        ],
                    ],
                ],
            ]
        );

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/payments',
                    'Body' => [
                        'Body' => [
                            'Merchant' => 4,
                            'Order' => [
                                'Transaction' => [
                                    'Type'          => 'Sale',
                                    'Currency'      => 'USD',
                                    'Amount'        => 10000,
                                    'InvoiceNumber' => null,
                                    'ExternalId'    => null,
                                    'Comment1'      => null,
                                    'Comment2'      => null,
                                    'SoftDescriptor' => 'BSPAY - Payment',
                                    'SplitAmount'   => 1000,
                                    'SplitMerchant' => 2,
                                ],
                                'PaymentMethod' => 3
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => hash("sha256",json_encode($curlProvider->calls[0]["Body"]["Body"]).$merchantHash)
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => hash("sha256",json_encode($curlProvider->calls[0]["Body"]["Body"]).$merchantHash)],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }
}
