<?php

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\AccountTypes;
use StackPay\Payments\Currency;
use StackPay\Payments\Exceptions;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class CreatePaymentMethodWithTokenTest extends TestCase
{
    public function testSuccessfulCase()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"a386800a63f01bbe7a8ab293ba2378849d5fa290d6ad3f086f50e2fb2892497f"}},"Body":{"Status":1,"Customer":526,"PaymentMethod":{"ID":510,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":1,"ExpirationYear":2001,"BillingAddress":{"AddressLine1":"1234 Windall Lane","AddressLine2":"","City":"Nowhere","State":"HI","Zip":"89765","Country":"USA"}}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $paymentMethod = $sdk->createPaymentMethodWithToken(
            (new Structures\Token())->setToken('z2CsRkTedDEwI0b')
        );

        $this->assertEquals(
            [
                "Status"   => 1,
                "ID"       => $paymentMethod->id(),
                "Customer" => [
                    "ID" => 526,
                ],
                "Account" => [
                    "Type"              => AccountTypes::VISA,
                    "Last4"             => "1111",
                    "Expiration Month"  => 1,
                    "Expiration Year"   => "2001",
                    "Billing Address"   => [
                        "Address 1"     => "1234 Windall Lane",
                        "Address 2"     => "",
                        "City"          => "Nowhere",
                        "State"         => "HI",
                        "Postal Code"   => "89765",
                        "Country"       => "USA",
                    ],
                ],
            ],
            [
                "Status"    => $paymentMethod->status(),
                "ID"        => $paymentMethod->id(),
                "Customer"  => [
                    "ID" => $paymentMethod->customer()->id(),
                ],
                "Account"   => [
                    "Type"              => $paymentMethod->account()->type(),
                    "Last4"             => $paymentMethod->account()->last4(),
                    "Expiration Month"  => $paymentMethod->account()->expireMonth(),
                    "Expiration Year"   => $paymentMethod->account()->expireYear(),
                    "Billing Address"   => [
                        "Address 1"     => $paymentMethod->accountHolder()->billingAddress()->address1(),
                        "Address 2"     => $paymentMethod->accountHolder()->billingAddress()->address2(),
                        "City"          => $paymentMethod->accountHolder()->billingAddress()->city(),
                        "State"         => $paymentMethod->accountHolder()->billingAddress()->state(),
                        "Postal Code"   => $paymentMethod->accountHolder()->billingAddress()->postalCode(),
                        "Country"       => $paymentMethod->accountHolder()->billingAddress()->country(),
                    ],
                ],
            ]
        );

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/paymethods',
                    'Body' => [
                        'Body' => [
                            'Order' => [
                                'Token' => 'z2CsRkTedDEwI0b'
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '88f109b02c850d423e7daa5931542f7391408be2a22a95f8d58a1843d3f43e59'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '88f109b02c850d423e7daa5931542f7391408be2a22a95f8d58a1843d3f43e59'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testInvalidToken()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"error_code":404,"error_message":"Token is invalid or expired."}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        try {
            $paymentMethod = $sdk->createPaymentMethodWithToken(
                (new Structures\Token())->setToken('z2CsRkTedDEwI0b')
            );
        } catch (Exceptions\RequestErrorException $e) {
            $this->assertEquals('Token is invalid or expired.', $e->getMessage());
            $this->assertEquals(404, $e->getCode());
        } catch (\Exception $e) {
            $this->fail('Unexpected exception thrown: '. $e->getMessage());
        }

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/paymethods',
                    'Body' => [
                        'Body' => [
                            'Order' => [
                                'Token' => 'z2CsRkTedDEwI0b'
                            ]
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '88f109b02c850d423e7daa5931542f7391408be2a22a95f8d58a1843d3f43e59'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '88f109b02c850d423e7daa5931542f7391408be2a22a95f8d58a1843d3f43e59'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }
}
