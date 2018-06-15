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

final class SaleWithTokenTest extends TestCase
{
    public function testSucessfulCase()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"9226e2a993a2fa2ff036c72304f022364d1d0b6522aa7203850354d2b2ddee88"}},"Body":{"Status":1,"Merchant":4,"Order":558,"Transaction":727,"Payment":{"Customer":532,"PaymentMethod":null,"Amount":10000,"SplitMerchant":2,"SplitAmount":1000,"Currency":"USD","AuthorizationCode":"A11111","AVSCode":"T","CVVResponseCode":"NotPresent"},"PaymentMethod":{"ID":null,"AccountType":"visa","AccountLast4":"1111","ExpirationMonth":8,"ExpirationYear":2018,"BillingAddress":{"AddressLine1":"8100 SW Nyberg Rd","AddressLine2":"Ste 450","City":"Not Real City","State":"OK","Zip":"87609","Country":"USA"}}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $token = (new Structures\Token())
            ->setToken('PUgRqrhFIKH0BYX');

        $merchant =(new Structures\Merchant())
            ->setID(4)
            ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

        $splitMerchant =(new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $sale = $sdk->saleWithToken(
            $token,
            $merchant,
            10000,        // Amount
            null,
            $split,
            null,         // Idempotency Key
            Currency::USD
        );

        $this->assertEquals([
            "ID"                 => 727,
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
                "ID" => 558,
            ],
            "Customer" => [
                "ID" => 532,
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
                    "Expiration Month" => "8",
                    "Expiration Year"  => "2018",
                ],
                "Account Holder" => [
                    "Billing Address" => [
                        "Address 1" => "8100 SW Nyberg Rd",
                        "Address 2" => "Ste 450",
                        "City"      => "Not Real City",
                        "State"     => "OK",
                        "Postal Code" => "87609",
                        "Country"     => "USA",
                    ],
                ],
            ],
        ],[
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

        $this->assertCount(1,       $curlProvider->calls );

        $this->assertEquals([
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
                                'SplitAmount'   => 1000,
                                'SplitMerchant' => 2,
                            ],
                            'Token' => 'PUgRqrhFIKH0BYX'
                        ]
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => 'v1',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => '232fb918e6c971bbb53a2d68a66533e2681fec048353019966a37301e0c49420'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => '232fb918e6c971bbb53a2d68a66533e2681fec048353019966a37301e0c49420'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
            ], $curlProvider->calls );
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

        try
        {
            $token = (new Structures\Token())
                ->setToken('PUgRqrhFIKH0BYX');

            $merchant = (new Structures\Merchant())
                ->setID(4)
                ->setHashKey('f72d6a9fab75e16a7219430f2a60d9cbd7f60b304b4c1a8d98d4e54d695b61e8');

            $splitMerchant = (new Structures\Merchant())
                ->setID(2);

            $split = (new Structures\Split())
                ->setAmount(1000)
                ->setMerchant($splitMerchant);

            $sale = $sdk->saleWithToken(
                $token,
                $merchant,
                10000,        // Amount
                null,
                $split,
                null,         // Idempotency Key
                Currency::USD
            );
        } catch(Exceptions\RequestErrorException $e) {
            $this->assertEquals('Token is invalid or expired.', $e->message());
            $this->assertEquals(404,                            $e->code());
        } catch (\Exception $e) {
            $this->fail('Unexpected exception thrown: '. $e->getMessage());
        }

        $this->assertCount(1, $curlProvider->calls );

        $this->assertEquals([
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
                                'SplitAmount'   => 1000,
                                'SplitMerchant' => 2,
                            ],
                            'Token' => 'PUgRqrhFIKH0BYX'
                        ]
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => 'v1',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => '232fb918e6c971bbb53a2d68a66533e2681fec048353019966a37301e0c49420'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => '232fb918e6c971bbb53a2d68a66533e2681fec048353019966a37301e0c49420'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
            ], $curlProvider->calls );
    }
}
