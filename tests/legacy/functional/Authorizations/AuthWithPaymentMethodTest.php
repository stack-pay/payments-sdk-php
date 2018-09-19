<?php

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\AccountTypes;
use StackPay\Payments\Currency;
use StackPay\Payments\Exceptions;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class AuthWithPaymentMethodTest extends TestCase
{
    public function testSucessfulCase()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"8c61e61a55885f476cbf8b0e38aabc574f7d95995278cf2a87ae424272de5c16"}},"Body":{"Status":1,"Merchant":4,"Order":553,"Transaction":722,"Payment":{"Customer":3,"PaymentMethod":3,"Amount":10000,"SplitMerchant":2,"SplitAmount":1000,"Currency":"USD","AuthorizationCode":"A11111","AVSCode":"T","CVVResponseCode":"NotPresent"},"PaymentMethod":{"ID":3,"AccountType":"amex","AccountLast4":"4121","ExpirationMonth":7,"ExpirationYear":2027,"BillingAddress":{"AddressLine1":"69976 Mills Cliffs","AddressLine2":"Suite 479","City":"Feestfort","State":"VT","Zip":"04059-2412","Country":"USA"}}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $paymentMethod = (new Structures\PaymentMethod())
            ->setID(3);

        $merchant =(new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant =(new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $auth = $sdk->authWithPaymentMethod(
            $paymentMethod,
            $merchant,
            10000,        // Amount
            $split,
            null,         // Idempotency Key
            Currency::USD
        );

        $this->assertEquals(
            [
                "ID"                 => 722,
                "Status"             => 1,
                "Amount"             => 10000,
                "Currency"           => Currency::USD,
                "Authorization Code" => "A11111",
                "AVS Code"           => "T",
                "CVV Response Code"  => "NotPresent",
                "Merchant" => [
                    "ID" => 4,
                ],
                "Order" => [
                    "ID" => 553,
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
                "ID"                 => $auth->id(),
                "Status"             => $auth->status(),
                "Amount"             => $auth->amount(),
                "Currency"           => $auth->currency(),
                "Authorization Code" => $auth->authCode(),
                "AVS Code"           => $auth->avsCode(),
                "CVV Response Code"  => $auth->cvvResponseCode(),
                "Merchant" => [
                    "ID" => $auth->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $auth->order()->id(),
                ],
                "Customer" => [
                    "ID" => $auth->customer()->id(),
                ],
                "Split" => [
                    "Merchant" => [
                        "ID" => $auth->split()->merchant()->id(),
                    ],
                    "Amount" => $auth->split()->amount(),
                ],
                "Payment Method"     => [
                    "ID" => $auth->paymentMethod()->id(),
                    "Account" => [
                        "Type"             => $auth->paymentMethod()->account()->type(),
                        "Last 4"           => $auth->paymentMethod()->account()->last4(),
                        "Expiration Month" => $auth->paymentMethod()->account()->expireMonth(),
                        "Expiration Year"  => $auth->paymentMethod()->account()->expireYear(),
                    ],
                    "Account Holder" => [
                        "Billing Address" => [
                            "Address 1" => $auth->paymentMethod()->accountHolder()->billingAddress()->address1(),
                            "Address 2" => $auth->paymentMethod()->accountHolder()->billingAddress()->address2(),
                            "City"      => $auth->paymentMethod()->accountHolder()->billingAddress()->city(),
                            "State"     => $auth->paymentMethod()->accountHolder()->billingAddress()->state(),
                            "Postal Code" => $auth->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
                            "Country"     => $auth->paymentMethod()->accountHolder()->billingAddress()->country(),
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
                                    'Type'          => 'Auth',
                                    'Currency'      => 'USD',
                                    'Amount'        => 10000,
                                    'InvoiceNumber' => null,
                                    'ExternalId'    => null,
                                    'Comment1'      => null,
                                    'Comment2'      => null,
                                    'SplitAmount'   => 1000,
                                    'SplitMerchant' => 2,
                                ],
                                'PaymentMethod' => '3'
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '286f93cb4cee16eebc952aa8e589ed5e07202d57eceb5f9730cbacf658976fcd'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '286f93cb4cee16eebc952aa8e589ed5e07202d57eceb5f9730cbacf658976fcd'],
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
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"8c61e61a55885f476cbf8b0e38aabc574f7d95995278cf2a87ae424272de5c16"}},"Body":{"Status":1,"Merchant":4,"Order":553,"Transaction":722,"Payment":{"Customer":3,"PaymentMethod":3,"Amount":10000,"SplitMerchant":2,"SplitAmount":1000,"Currency":"USD","AuthorizationCode":"A11111","AVSCode":"T","CVVResponseCode":"NotPresent"},"PaymentMethod":{"ID":3,"AccountType":"amex","AccountLast4":"4121","ExpirationMonth":7,"ExpirationYear":2027,"BillingAddress":{"AddressLine1":"69976 Mills Cliffs","AddressLine2":"Suite 479","City":"Feestfort","State":"VT","Zip":"04059-2412","Country":"USA"}}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $paymentMethod = (new Structures\PaymentMethod())
            ->setID(3);

        $merchant =(new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant =(new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = \StackPay\Payments\Factories\Auth::withPaymentMethod(
            $paymentMethod,
            $merchant,
            10000,        // Amount
            $split,
            Currency::USD
        );

        $auth = $sdk->processTransaction($transaction);

        $this->assertEquals(
            [
                "ID"                 => 722,
                "Status"             => 1,
                "Amount"             => 10000,
                "Currency"           => Currency::USD,
                "Authorization Code" => "A11111",
                "AVS Code"           => "T",
                "CVV Response Code"  => "NotPresent",
                "Merchant" => [
                    "ID" => 4,
                ],
                "Order" => [
                    "ID" => 553,
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
                "ID"                 => $auth->id(),
                "Status"             => $auth->status(),
                "Amount"             => $auth->amount(),
                "Currency"           => $auth->currency(),
                "Authorization Code" => $auth->authCode(),
                "AVS Code"           => $auth->avsCode(),
                "CVV Response Code"  => $auth->cvvResponseCode(),
                "Merchant" => [
                    "ID" => $auth->merchant()->id(),
                ],
                "Order" => [
                    "ID" => $auth->order()->id(),
                ],
                "Customer" => [
                    "ID" => $auth->customer()->id(),
                ],
                "Split" => [
                    "Merchant" => [
                        "ID" => $auth->split()->merchant()->id(),
                    ],
                    "Amount" => $auth->split()->amount(),
                ],
                "Payment Method"     => [
                    "ID" => $auth->paymentMethod()->id(),
                    "Account" => [
                        "Type"             => $auth->paymentMethod()->account()->type(),
                        "Last 4"           => $auth->paymentMethod()->account()->last4(),
                        "Expiration Month" => $auth->paymentMethod()->account()->expireMonth(),
                        "Expiration Year"  => $auth->paymentMethod()->account()->expireYear(),
                    ],
                    "Account Holder" => [
                        "Billing Address" => [
                            "Address 1" => $auth->paymentMethod()->accountHolder()->billingAddress()->address1(),
                            "Address 2" => $auth->paymentMethod()->accountHolder()->billingAddress()->address2(),
                            "City"      => $auth->paymentMethod()->accountHolder()->billingAddress()->city(),
                            "State"     => $auth->paymentMethod()->accountHolder()->billingAddress()->state(),
                            "Postal Code" => $auth->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
                            "Country"     => $auth->paymentMethod()->accountHolder()->billingAddress()->country(),
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
                                    'Type'          => 'Auth',
                                    'Currency'      => 'USD',
                                    'Amount'        => 10000,
                                    'InvoiceNumber' => null,
                                    'ExternalId'    => null,
                                    'Comment1'      => null,
                                    'Comment2'      => null,
                                    'SplitAmount'   => 1000,
                                    'SplitMerchant' => 2,
                                ],
                                'PaymentMethod' => '3'
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '286f93cb4cee16eebc952aa8e589ed5e07202d57eceb5f9730cbacf658976fcd'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '286f93cb4cee16eebc952aa8e589ed5e07202d57eceb5f9730cbacf658976fcd'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }
}
