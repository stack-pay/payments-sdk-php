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

final class CaptureWithOriginalTransactionTest extends TestCase
{
    public function testSucessfulCase()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"bd504988f4d00bfc027812d2b5db5b2f00af8c368e1d5e2794edfdb69424785e"}},"Body":{"Status":1,"Merchant":4,"Order":563,"Transaction":735,"Payment":{"Customer":1,"PaymentMethod":null,"Amount":90,"SplitMerchant":2,"SplitAmount":20,"Currency":"USD","AuthorizationCode":null,"AVSCode":"NotPresent","CVVResponseCode":"NotPresent","CapturedTransaction":734},"PaymentMethod":{"ID":null,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":8,"ExpirationYear":2018,"BillingAddress":{"AddressLine1":"8100 SW Nyberg Rd","AddressLine2":"Ste 450","City":"Not Real City","State":"OK","Zip":"87609","Country":"USA"}}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $auth = (new Structures\Auth())
            ->setID(734);

        $merchant =(new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $capture = $sdk->captureWithOriginalTransaction(
            $auth,
            90,
            20,
            $merchant
        );

        $this->assertEquals(
            [
                "ID"                    => 735,
                "Status"                => 1,
                "Amount"                => 90,
                "Currency"              => Currency::USD,
                "Authorization Code"    => null,
                "AVS Code"              => "NotPresent",
                "CVV Response Code"     => "NotPresent",
                "Captured Transaction"  => [
                    "ID" => "734",
                ],
                "Merchant" => [
                    "ID" => 4,
                ],
                "Order" => [
                    "ID" => 563,
                ],
                "Customer" => [
                    "ID" => 1,
                ],
                "Split" => [
                    "Merchant" => [
                        "ID" => 2,
                    ],
                    "Amount" => 20,
                ],
                "Payment Method"     => [
                    "ID" => null,
                    "Account" => [
                        "Type"              => AccountTypes::VISA,
                        "Last 4"            => "1111",
                        "Expiration Month"  => "8",
                        "Expiration Year"   => "2018",
                    ],
                    "Account Holder" => [
                        "Billing Address" => [
                            "Address 1"     => "8100 SW Nyberg Rd",
                            "Address 2"     => "Ste 450",
                            "City"          => "Not Real City",
                            "State"         => "OK",
                            "Postal Code"   => "87609",
                            "Country"       => "USA",
                        ],
                    ],
                ],
            ],
            [
                "ID"                    => $capture->id(),
                "Status"                => $capture->status(),
                "Amount"                => $capture->amount(),
                "Currency"              => $capture->currency(),
                "Authorization Code"    => $capture->authCode(),
                "AVS Code"              => $capture->avsCode(),
                "CVV Response Code"     => $capture->cvvResponseCode(),
                "Captured Transaction"  => [
                    "ID" => $capture->capturedTransaction()->id(),
                ],
                "Merchant" => [
                    "ID" => $capture->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $capture->order()->id(),
                ],
                "Customer" => [
                    "ID" => $capture->customer()->id(),
                ],
                "Split" => [
                    "Merchant" => [
                        "ID" => $capture->split()->merchant()->id(),
                    ],
                    "Amount" => $capture->split()->amount(),
                ],
                "Payment Method"     => [
                    "ID"        => $capture->paymentMethod()->id(),
                    "Account"   => [
                        "Type"              => $capture->paymentMethod()->account()->type(),
                        "Last 4"            => $capture->paymentMethod()->account()->last4(),
                        "Expiration Month"  => $capture->paymentMethod()->account()->expireMonth(),
                        "Expiration Year"   => $capture->paymentMethod()->account()->expireYear(),
                    ],
                    "Account Holder" => [
                        "Billing Address" => [
                            "Address 1"     => $capture->paymentMethod()->accountHolder()->billingAddress()->address1(),
                            "Address 2"     => $capture->paymentMethod()->accountHolder()->billingAddress()->address2(),
                            "City"          => $capture->paymentMethod()->accountHolder()->billingAddress()->city(),
                            "State"         => $capture->paymentMethod()->accountHolder()->billingAddress()->state(),
                            "Postal Code"   => $capture->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
                            "Country"       => $capture->paymentMethod()->accountHolder()->billingAddress()->country(),
                        ],
                    ],
                ],
            ]
        );

        $this->assertNull($capture->authCode);

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
                                    'Type'          => 'Capture',
                                    'Amount'        => 90,
                                    'SplitAmount'   => 20,
                                ],
                                'OriginalTransaction' => 734
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '332d0ff02a99154d28aee411903dbd083168230b76ca018d7f828cc571c6009a'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '332d0ff02a99154d28aee411903dbd083168230b76ca018d7f828cc571c6009a'],
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
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"bd504988f4d00bfc027812d2b5db5b2f00af8c368e1d5e2794edfdb69424785e"}},"Body":{"Status":1,"Merchant":4,"Order":563,"Transaction":735,"Payment":{"Customer":1,"PaymentMethod":null,"Amount":90,"SplitMerchant":2,"SplitAmount":20,"Currency":"USD","AuthorizationCode":null,"AVSCode":"NotPresent","CVVResponseCode":"NotPresent","CapturedTransaction":734},"PaymentMethod":{"ID":null,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":8,"ExpirationYear":2018,"BillingAddress":{"AddressLine1":"8100 SW Nyberg Rd","AddressLine2":"Ste 450","City":"Not Real City","State":"OK","Zip":"87609","Country":"USA"}}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $merchant =(new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $auth = (new Structures\Auth())
            ->setID(734)
            ->setMerchant($merchant);

        $split = (new Structures\Split())
            ->setAmount(20);

        $transaction = Factories\Capture::previousTransaction(
            $auth,
            90,
            $split
        );

        $capture = $sdk->processTransaction($transaction);

        $this->assertEquals(
            [
                "ID"                    => 735,
                "Status"                => 1,
                "Amount"                => 90,
                "Currency"              => Currency::USD,
                "Authorization Code"    => null,
                "AVS Code"              => "NotPresent",
                "CVV Response Code"     => "NotPresent",
                "Captured Transaction"  => [
                    "ID" => "734",
                ],
                "Merchant" => [
                    "ID" => 4,
                ],
                "Order" => [
                    "ID" => 563,
                ],
                "Customer" => [
                    "ID" => 1,
                ],
                "Split" => [
                    "Merchant" => [
                        "ID" => 2,
                    ],
                    "Amount" => 20,
                ],
                "Payment Method" => [
                    "ID"        => null,
                    "Account"   => [
                        "Type"              => AccountTypes::VISA,
                        "Last 4"            => "1111",
                        "Expiration Month"  => "8",
                        "Expiration Year"   => "2018",
                    ],
                    "Account Holder" => [
                        "Billing Address" => [
                            "Address 1"     => "8100 SW Nyberg Rd",
                            "Address 2"     => "Ste 450",
                            "City"          => "Not Real City",
                            "State"         => "OK",
                            "Postal Code"   => "87609",
                            "Country"       => "USA",
                        ],
                    ],
                ],
            ],
            [
                "ID"                    => $capture->id(),
                "Status"                => $capture->status(),
                "Amount"                => $capture->amount(),
                "Currency"              => $capture->currency(),
                "Authorization Code"    => $capture->authCode(),
                "AVS Code"              => $capture->avsCode(),
                "CVV Response Code"     => $capture->cvvResponseCode(),
                "Captured Transaction"  => [
                    "ID" => $capture->capturedTransaction()->id(),
                ],
                "Merchant" => [
                    "ID" => $capture->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $capture->order()->id(),
                ],
                "Customer" => [
                    "ID" => $capture->customer()->id(),
                ],
                "Split" => [
                    "Merchant" => [
                        "ID" => $capture->split()->merchant()->id(),
                    ],
                    "Amount" => $capture->split()->amount(),
                ],
                "Payment Method" => [
                    "ID"        => $capture->paymentMethod()->id(),
                    "Account"   => [
                        "Type"              => $capture->paymentMethod()->account()->type(),
                        "Last 4"            => $capture->paymentMethod()->account()->last4(),
                        "Expiration Month"  => $capture->paymentMethod()->account()->expireMonth(),
                        "Expiration Year"   => $capture->paymentMethod()->account()->expireYear(),
                    ],
                    "Account Holder" => [
                        "Billing Address" => [
                            "Address 1"     => $capture->paymentMethod()->accountHolder()->billingAddress()->address1(),
                            "Address 2"     => $capture->paymentMethod()->accountHolder()->billingAddress()->address2(),
                            "City"          => $capture->paymentMethod()->accountHolder()->billingAddress()->city(),
                            "State"         => $capture->paymentMethod()->accountHolder()->billingAddress()->state(),
                            "Postal Code"   => $capture->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
                            "Country"       => $capture->paymentMethod()->accountHolder()->billingAddress()->country(),
                        ],
                    ],
                ],
            ]
        );

        $this->assertNull($capture->authCode);

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
                                    'Type'          => 'Capture',
                                    'Amount'        => 90,
                                    'SplitAmount'   => 20,
                                ],
                                'OriginalTransaction' => 734
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '332d0ff02a99154d28aee411903dbd083168230b76ca018d7f828cc571c6009a'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '332d0ff02a99154d28aee411903dbd083168230b76ca018d7f828cc571c6009a'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }
}
