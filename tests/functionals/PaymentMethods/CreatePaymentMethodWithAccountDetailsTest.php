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

final class CreatePaymentMethodWithAccountDetailsTest extends TestCase
{
    public function testCreditCard()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"689cafab28f1fbe82ecebff8f1fac16f759eea1fce1def98f785b4f1fa29761d"}},"Body":{"Status":1,"Customer":527,"PaymentMethod":{"ID":511,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":1,"ExpirationYear":"2001","BillingAddress":{"AddressLine1":"1234 Windall Lane","AddressLine2":"","City":"Nowhere","State":"HI","Zip":"89765","Country":"USA"}}}}'
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
            ->setExpireDate('0101');

        $address = (new Structures\Address())
            ->setAddress1('1234 Windall Lane')
            ->setCity('Nowhere')
            ->setState('HI')
            ->setPostalCode('89765')
            ->setCountry('usa');

        $accountHolder = (new Structures\AccountHolder())
            ->setName('John Doe')
            ->setBillingAddress($address);

        $paymentMethod = $sdk->createPaymentMethodWithAccountDetails(
            $account,
            $accountHolder
        );

        $this->assertEquals([
            "Status"   => 1,
            "ID"       => $paymentMethod->id(),
            "Customer" => [
                "ID" => 527,
            ],
            "Account" => [
                "Type"             => AccountTypes::VISA,
                "Last4"            => "1111",
                "Expiration Month" => 1,
                "Expiration Year"  => "2001",
                "Billing Address" => [
                    "Address 1"   => "1234 Windall Lane",
                    "Address 2"   => "",
                    "City"        => "Nowhere",
                    "State"       => "HI",
                    "Postal Code" => "89765",
                    "Country"     => "USA",
                ],
            ],
        ],[
            "Status"   => $paymentMethod->status(),
            "ID"       => $paymentMethod->id(),
            "Customer" => [
                "ID" => $paymentMethod->customer()->id(),
            ],
            "Account" => [
                "Type"             => $paymentMethod->account()->type(),
                "Last4"            => $paymentMethod->account()->last4(),
                "Expiration Month" => $paymentMethod->account()->expireMonth(),
                "Expiration Year"  => $paymentMethod->account()->expireYear(),
                "Billing Address" => [
                    "Address 1"   => $paymentMethod->accountHolder()->billingAddress()->address1(),
                    "Address 2"   => $paymentMethod->accountHolder()->billingAddress()->address2(),
                    "City"        => $paymentMethod->accountHolder()->billingAddress()->city(),
                    "State"       => $paymentMethod->accountHolder()->billingAddress()->state(),
                    "Postal Code" => $paymentMethod->accountHolder()->billingAddress()->postalCode(),
                    "Country"     => $paymentMethod->accountHolder()->billingAddress()->country(),
                ],
            ],
        ]);

        $this->assertCount(1,       $curlProvider->calls);

        $this->assertEquals([
            0 => [
                'URL'  => 'https://api.mystackpay.com/api/paymethods',
                'Body' => [
                    'Body' => [
                        'Order' => [
                            'Account'       => [
                                'Type'       => 'visa',
                                'Number'     => '4111111111111111',
                                'ExpireDate' => '0101',
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
                            ]
                        ]
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => '1.0.0',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => '03534379d1ed4aecb54d3b9ced0fff3fb95234bc261068d151d761ca9f786ded'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => '1.0.0'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => '03534379d1ed4aecb54d3b9ced0fff3fb95234bc261068d151d761ca9f786ded'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
            ], $curlProvider->calls );
    }

    public function testBankAccount()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"689cafab28f1fbe82ecebff8f1fac16f759eea1fce1def98f785b4f1fa29761d"}},"Body":{"Status":1,"Customer":527,"PaymentMethod":{"ID":511,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":1,"ExpirationYear":"2001","BillingAddress":{"AddressLine1":"1234 Windall Lane","AddressLine2":"","City":"Nowhere","State":"HI","Zip":"89765","Country":"USA"}}}}'
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

        $paymentMethod = $sdk->createPaymentMethodWithAccountDetails(
            $account,
            $accountHolder
        );

        $this->assertEquals([
            "Status"   => 1,
            "ID"       => $paymentMethod->id(),
            "Customer" => [
                "ID" => 527,
            ],
            "Account" => [
                "Type"             => AccountTypes::VISA,
                "Last4"            => "1111",
                "Expiration Month" => 1,
                "Expiration Year"  => "2001",
                "Billing Address" => [
                    "Address 1"   => "1234 Windall Lane",
                    "Address 2"   => "",
                    "City"        => "Nowhere",
                    "State"       => "HI",
                    "Postal Code" => "89765",
                    "Country"     => "USA",
                ],
            ],
        ],[
            "Status"   => $paymentMethod->status(),
            "ID"       => $paymentMethod->id(),
            "Customer" => [
                "ID" => $paymentMethod->customer()->id(),
            ],
            "Account" => [
                "Type"             => $paymentMethod->account()->type(),
                "Last4"            => $paymentMethod->account()->last4(),
                "Expiration Month" => $paymentMethod->account()->expireMonth(),
                "Expiration Year"  => $paymentMethod->account()->expireYear(),
                "Billing Address" => [
                    "Address 1"   => $paymentMethod->accountHolder()->billingAddress()->address1(),
                    "Address 2"   => $paymentMethod->accountHolder()->billingAddress()->address2(),
                    "City"        => $paymentMethod->accountHolder()->billingAddress()->city(),
                    "State"       => $paymentMethod->accountHolder()->billingAddress()->state(),
                    "Postal Code" => $paymentMethod->accountHolder()->billingAddress()->postalCode(),
                    "Country"     => $paymentMethod->accountHolder()->billingAddress()->country(),
                ],
            ],
        ]);

        $this->assertCount(1,       $curlProvider->calls);

        $this->assertEquals([
            0 => [
                'URL'  => 'https://api.mystackpay.com/api/paymethods',
                'Body' => [
                    'Body' => [
                        'Order' => [
                            'Account'       => [
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
                            ]
                        ]
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => '1.0.0',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => '1ade4a2ad4985192f3e8a0f5c8142a1207db0231500bc3cf4f1165c521bba83d'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => '1.0.0'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => '1ade4a2ad4985192f3e8a0f5c8142a1207db0231500bc3cf4f1165c521bba83d'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
            ], $curlProvider->calls );
    }

    public function testInvalidAccountTypeException()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"689cafab28f1fbe82ecebff8f1fac16f759eea1fce1def98f785b4f1fa29761d"}},"Body":{"Status":1,"Customer":527,"PaymentMethod":{"ID":511,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":1,"ExpirationYear":"2001","BillingAddress":{"AddressLine1":"1234 Windall Lane","AddressLine2":"","City":"Nowhere","State":"HI","Zip":"89765","Country":"USA"}}}}'
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

        try {
            $paymentMethod = $sdk->createPaymentMethodWithAccountDetails(
                $account,
                $accountHolder
            );
        } catch(Exceptions\InvalidAccountTypeException $e) {
            $this->assertEquals(
                "The supplied AccountType(INVALID ACCOUNT TYPE) is invalid",
                $e->getMessage()
            );
        } catch(\Exception $e) {
            $this->fail("Unexcepted Exception:\n\t$e->getMessage()");
        }

        $this->assertCount(0,       $curlProvider->calls);
    }
}
