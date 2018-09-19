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

final class SaleWithAccountDetailsTest extends TestCase
{
    public function testWithCreditCard()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"ace77eba288f9f948a97d802b5cf7115f1127e2d77c0bf79c34d2ff33ebbfe58"}},"Body":{"Status":1,"Merchant":4,"Order":560,"Transaction":729,"Payment":{"Customer":535,"PaymentMethod":null,"Amount":10000,"SplitMerchant":2,"SplitAmount":1000,"Currency":"USD","AuthorizationCode":"A11111","AVSCode":"T","CVVResponseCode":"NotPresent"},"PaymentMethod":{"ID":null,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":1,"ExpirationYear":"2021","BillingAddress":{"AddressLine1":"1234 Windall Lane","AddressLine2":"","City":"Nowhere","State":"HI","Zip":"89765","Country":"USA"}}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $account = (new Structures\Account())
            ->setType(AccountTypes::VISA)
            ->setNumber('4111111111111111')
            ->setCVV2('888')
            ->setExpireDate('0121');

        $address = (new Structures\Address())
            ->setAddress1('1234 Windall Lane')
            ->setCity('Nowhere')
            ->setState('HI')
            ->setPostalCode('89765')
            ->setCountry('usa');

        $accountHolder = (new Structures\AccountHolder())
            ->setName('John Doe')
            ->setBillingAddress($address);

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $sale = $sdk->saleWithAccountDetails(
            $account,
            $accountHolder,
            $merchant,
            10000,        // Amount
            null,
            $split,
            null,         // Idempotency Key
            Currency::USD
        );

        $this->assertEquals([
            "ID"                 => 729,
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
                "ID" => 560,
            ],
            "Customer" => [
                "ID" => 535,
            ],
            "Split" => [
                "Merchant" => [
                    "ID" => 2,
                ],
                "Amount" => 1000,
            ],
            "Payment Method"     => [
                "ID" => null,
                "Account" => [
                    "Type"             => AccountTypes::VISA,
                    "Last 4"           => "1111",
                    "Expiration Month" => "1",
                    "Expiration Year"  => "2021",
                ],
                "Account Holder" => [
                    "Billing Address" => [
                        "Address 1" => "1234 Windall Lane",
                        "Address 2" => "",
                        "City"      => "Nowhere",
                        "State"     => "HI",
                        "Postal Code" => "89765",
                        "Country"     => "USA",
                    ],
                ],
            ],
        ], [
            "ID"                 => $sale->id(),
            "Status"             => $sale->status(),
            "Amount"             => $sale->amount(),
            "Currency"           => $sale->currency(),
            "Authorization Code" => $sale->authCode(),
            "AVS Code"           => $sale->avsCode(),
            "CVV Response Code"  => $sale->cvvResponseCode(),
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
        ]);

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
                                    'SplitAmount'   => 1000,
                                    'SplitMerchant' => 2,
                                ],
                                'Account' => [
                                    'Type'       => 'visa',
                                    'Number'     => '4111111111111111',
                                    'ExpireDate' => '0121',
                                    'Cvv2'       => '888'
                                ],
                                'AccountHolder' => [
                                    'Name'           => 'John Doe',
                                    'BillingAddress' => [
                                        'City'     => 'Nowhere',
                                        'State'    => 'HI',
                                        'Zip'      => '89765',
                                        'Country'  => 'usa',
                                        'Address1' => '1234 Windall Lane',
                                        'Address2' => ''
                                    ]
                                ],
                                'SavePaymentMethod' => null
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '4684e9024038f4fb42424f38a743fcfa2bf32fd45585ffb955038d9d6f91bbb5'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '4684e9024038f4fb42424f38a743fcfa2bf32fd45585ffb955038d9d6f91bbb5'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json'],
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testWithBankAccount()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"ace77eba288f9f948a97d802b5cf7115f1127e2d77c0bf79c34d2ff33ebbfe58"}},"Body":{"Status":1,"Merchant":4,"Order":560,"Transaction":729,"Payment":{"Customer":535,"PaymentMethod":null,"Amount":10000,"SplitMerchant":2,"SplitAmount":1000,"Currency":"USD","AuthorizationCode":"A11111","AVSCode":"T","CVVResponseCode":"NotPresent"},"PaymentMethod":{"ID":null,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":1,"ExpirationYear":"2021","BillingAddress":{"AddressLine1":"1234 Windall Lane","AddressLine2":"","City":"Nowhere","State":"HI","Zip":"89765","Country":"USA"}}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $account = (new Structures\Account())
            ->setType(AccountTypes::CHECKING)
            ->setNumber('4111111111111111')
            ->setRoutingNumber('8765309');

        $address = (new Structures\Address())
            ->setAddress1('1234 Windall Lane')
            ->setCity('Nowhere')
            ->setState('HI')
            ->setPostalCode('89765')
            ->setCountry('usa');

        $accountHolder = (new Structures\AccountHolder())
            ->setName('John Doe')
            ->setBillingAddress($address);

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $sale = $sdk->saleWithAccountDetails(
            $account,
            $accountHolder,
            $merchant,
            10000,        // Amount
            null,
            $split,
            null,         // Idempotency Key
            Currency::USD
        );

        $this->assertEquals([
            "ID"                 => 729,
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
                "ID" => 560,
            ],
            "Customer" => [
                "ID" => 535,
            ],
            "Split" => [
                "Merchant" => [
                    "ID" => 2,
                ],
                "Amount" => 1000,
            ],
            "Payment Method"     => [
                "ID" => null,
                "Account" => [
                    "Type"             => AccountTypes::VISA,
                    "Last 4"           => "1111",
                    "Expiration Month" => "1",
                    "Expiration Year"  => "2021",
                ],
                "Account Holder" => [
                    "Billing Address" => [
                        "Address 1" => "1234 Windall Lane",
                        "Address 2" => "",
                        "City"      => "Nowhere",
                        "State"     => "HI",
                        "Postal Code" => "89765",
                        "Country"     => "USA",
                    ],
                ],
            ],
        ], [
            "ID"                 => $sale->id(),
            "Status"             => $sale->status(),
            "Amount"             => $sale->amount(),
            "Currency"           => $sale->currency(),
            "Authorization Code" => $sale->authCode(),
            "AVS Code"           => $sale->avsCode(),
            "CVV Response Code"  => $sale->cvvResponseCode(),
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
        ]);

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
                                    'SplitAmount'   => 1000,
                                    'SplitMerchant' => 2,
                                ],
                                'Account' => [
                                    'Type'          => 'checking',
                                    'Number'        => '4111111111111111',
                                    'RoutingNumber' => '8765309',
                                ],
                                'AccountHolder' => [
                                    'Name'           => 'John Doe',
                                    'BillingAddress' => [
                                        'City'     => 'Nowhere',
                                        'State'    => 'HI',
                                        'Zip'      => '89765',
                                        'Country'  => 'usa',
                                        'Address1' => '1234 Windall Lane',
                                        'Address2' => ''
                                    ]
                                ],
                                'SavePaymentMethod' => null
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '0eff3ee9c5ddf616cd2b42be17bad4073cb662c833fb437c75cc603df8d6b9a7'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '0eff3ee9c5ddf616cd2b42be17bad4073cb662c833fb437c75cc603df8d6b9a7'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json'],
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testInvalidAccountTypeException()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"ace77eba288f9f948a97d802b5cf7115f1127e2d77c0bf79c34d2ff33ebbfe58"}},"Body":{"Status":1,"Merchant":4,"Order":560,"Transaction":729,"Payment":{"Customer":535,"PaymentMethod":null,"Amount":10000,"SplitMerchant":2,"SplitAmount":1000,"Currency":"USD","AuthorizationCode":"A11111","AVSCode":"T","CVVResponseCode":"NotPresent"},"PaymentMethod":{"ID":null,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":1,"ExpirationYear":"2021","BillingAddress":{"AddressLine1":"1234 Windall Lane","AddressLine2":"","City":"Nowhere","State":"HI","Zip":"89765","Country":"USA"}}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $account = (new Structures\Account())
            ->setType('INVALID ACCOUNT TYPE')
            ->setNumber('4111111111111111')
            ->setRoutingNumber('8765309');

        $address = (new Structures\Address())
            ->setAddress1('1234 Windall Lane')
            ->setCity('Nowhere')
            ->setState('HI')
            ->setPostalCode('89765')
            ->setCountry('usa');

        $accountHolder = (new Structures\AccountHolder())
            ->setName('John Doe')
            ->setBillingAddress($address);

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        try {
            $sale = $sdk->saleWithAccountDetails(
                $account,
                $accountHolder,
                $merchant,
                10000,        // Amount
                null,
                $split,
                null,         // Idempotency Key
                Currency::USD
            );
        } catch (Exceptions\InvalidAccountTypeException $e) {
            $this->assertEquals(
                "The supplied AccountType(INVALID ACCOUNT TYPE) is invalid",
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->fail("Unexcepted Exception:\n\t$e->getMessage()");
        }

        $this->assertCount(0, $curlProvider->calls);
    }

    public function testWithFactory()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"ace77eba288f9f948a97d802b5cf7115f1127e2d77c0bf79c34d2ff33ebbfe58"}},"Body":{"Status":1,"Merchant":4,"Order":560,"Transaction":729,"Payment":{"Customer":535,"PaymentMethod":null,"Amount":10000,"SplitMerchant":2,"SplitAmount":1000,"Currency":"USD","AuthorizationCode":"A11111","AVSCode":"T","CVVResponseCode":"NotPresent"},"PaymentMethod":{"ID":null,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":1,"ExpirationYear":"2021","BillingAddress":{"AddressLine1":"1234 Windall Lane","AddressLine2":"","City":"Nowhere","State":"HI","Zip":"89765","Country":"USA"}}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $account = (new Structures\Account())
            ->setType(AccountTypes::VISA)
            ->setNumber('4111111111111111')
            ->setCVV2('888')
            ->setExpireDate('0121');

        $address = (new Structures\Address())
            ->setAddress1('1234 Windall Lane')
            ->setCity('Nowhere')
            ->setState('HI')
            ->setPostalCode('89765')
            ->setCountry('usa');

        $accountHolder = (new Structures\AccountHolder())
            ->setName('John Doe')
            ->setBillingAddress($address);

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = Factories\Sale::withAccountDetails(
            $account,
            $accountHolder,
            $merchant,
            10000,        // Amount
            null,
            $split,
            Currency::USD
        );

        $sale = $sdk->processTransaction($transaction);

        $this->assertEquals([
            "ID"                 => 729,
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
                "ID" => 560,
            ],
            "Customer" => [
                "ID" => 535,
            ],
            "Split" => [
                "Merchant" => [
                    "ID" => 2,
                ],
                "Amount" => 1000,
            ],
            "Payment Method"     => [
                "ID" => null,
                "Account" => [
                    "Type"             => AccountTypes::VISA,
                    "Last 4"           => "1111",
                    "Expiration Month" => "1",
                    "Expiration Year"  => "2021",
                ],
                "Account Holder" => [
                    "Billing Address" => [
                        "Address 1" => "1234 Windall Lane",
                        "Address 2" => "",
                        "City"      => "Nowhere",
                        "State"     => "HI",
                        "Postal Code" => "89765",
                        "Country"     => "USA",
                    ],
                ],
            ],
        ], [
            "ID"                 => $sale->id(),
            "Status"             => $sale->status(),
            "Amount"             => $sale->amount(),
            "Currency"           => $sale->currency(),
            "Authorization Code" => $sale->authCode(),
            "AVS Code"           => $sale->avsCode(),
            "CVV Response Code"  => $sale->cvvResponseCode(),
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
        ]);

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
                                    'SplitAmount'   => 1000,
                                    'SplitMerchant' => 2,
                                ],
                                'Account' => [
                                    'Type'       => 'visa',
                                    'Number'     => '4111111111111111',
                                    'ExpireDate' => '0121',
                                    'Cvv2'       => '888'
                                ],
                                'AccountHolder' => [
                                    'Name'           => 'John Doe',
                                    'BillingAddress' => [
                                        'City'     => 'Nowhere',
                                        'State'    => 'HI',
                                        'Zip'      => '89765',
                                        'Country'  => 'usa',
                                        'Address1' => '1234 Windall Lane',
                                        'Address2' => ''
                                    ]
                                ],
                                'SavePaymentMethod' => null
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '4684e9024038f4fb42424f38a743fcfa2bf32fd45585ffb955038d9d6f91bbb5'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '4684e9024038f4fb42424f38a743fcfa2bf32fd45585ffb955038d9d6f91bbb5'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json'],
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }
}
