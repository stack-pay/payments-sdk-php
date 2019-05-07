<?php

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Exceptions;
use StackPay\Payments\Factories;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class CreateScheduledTransactionWithTokenTest extends TestCase
{
    public function testSuccessfulCase()
    {

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $merchantHash = '4a8fdc1e56261a0b2b2932bd3fb626b9127ae32cd440e9bfa1ad7a7cfce0ddaa';

        $curlBody = [
            'data' => [
                'id'                => 206,
                'merchant_id'       => 4,
                'scheduled_at'      => '2018-01-10',
                'currency_code'     => 'USD',
                'amount'            => 25000,
                'status'            => 'scheduled',
                'split_amount'      => 1000,
                'split_merchant_id' => 2,
                'soft_descriptor'   => 'BSPAY - Scheduled Payment',
                'payment_method' => [
                    'id'                => 392,
                    'customer_id'       => 400,
                    'address_1'         => '123 Thumble Lane',
                    'address_2'         => 'Apt. 765',
                    'city'              => 'New York',
                    'zip'               => '12345',
                    'state'             => 'NY',
                    'country'           => 'USA',
                    'type'              => 'credit_card',
                    'issuer'            => 'visa',
                    'card_number_last4' => '1111',
                    'expire_month'      => 8,
                    'expire_year'       => 2019
                ]

            ],
            'meta' => [
                'status' => 1
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

        $scheduledAt = new DateTime('2018-01-10 12:00');
        $scheduledAt->setTimezone(new DateTimeZone('America/New_York'));

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey($merchantHash);

        $token = (new Structures\Token())
            ->setToken('GMtaX5dzPe3DBUz');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = (new Structures\ScheduledTransaction())
            ->setToken($token)
            ->setMerchant($merchant)
            ->setAmount(10000)
            ->setScheduledAt($scheduledAt)
            ->setCurrencyCode(Currency::USD)
            ->setSplit($split);


        $scheduledTransaction = $sdk->createScheduledTransaction($transaction);

        $this->assertEquals(
            [
                'merchant_id'       => 4,
                'scheduled_at'      => '2018-01-10',
                'timezone'          => 'America/New_York',
                'currency_code'     => 'USD',
                'amount'            => 10000, //amount
                'split_amount'      => 1000,
                'split_merchant_id' => 2,
                'soft_descriptor'   => 'BSPAY - Scheduled Payment',
                'payment_method'    => [
                    'method'    => 'token',
                    'token'     => $token->token(),
                ],
            ],
            [
                'merchant_id'       => $scheduledTransaction->merchant()->id(),
                'scheduled_at'      => $scheduledTransaction->scheduledAt()->format('Y-m-d'),
                'timezone'          => $scheduledTransaction->scheduledAt()->getTimezone()->getName(),
                'currency_code'     => $scheduledTransaction->currencyCode(),
                'amount'            => $scheduledTransaction->amount(),
                'split_amount'      => $scheduledTransaction->split()->amount(),
                'split_merchant_id' => $scheduledTransaction->split()->merchant()->id(),
                'payment_method'    => [
                    'method'    => 'token',
                    'token'     => $scheduledTransaction->token()->token(),
                ],
            ]
        );

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/scheduled-transactions',
                    'Body' => [
                        'Body' => [
                            'merchant_id'       => 4,
                            'external_id'       => null,
                            'scheduled_at'      => '2018-01-10',
                            'timezone'          => 'America/New_York',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'split_amount'      => 1000,
                            'split_merchant_id' => 2,
                            'soft_descriptor'   => 'BSPAY - Scheduled Payment',
                            'payment_method'    => [
                                'method'    => 'token',
                                'token'     => $token->token(),
                            ],
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '4e8e9634c6aa44222d0c3b206d6b4ef449a92a2e9a2922774c2883a4006370a3'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '4e8e9634c6aa44222d0c3b206d6b4ef449a92a2e9a2922774c2883a4006370a3'],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testWithPaymentTokenFactory()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"d1fe5529c88d33eef7495162aa78f1af80db1ecad77be81c36fe1e13790e4f06"}},"Body":{"data":{"id":206,"merchant_id":4,"scheduled_at":"2018-01-10","currency_code":"USD","amount":25000,"status":"scheduled","split_amount":1000,"split_merchant_id":2,"payment_method":{"id":392,"customer_id":400,"address_1":"123 Thumble Lane","city":"New York","zip":"12345","address_2":"Apt. 765","state":"NY","country":"USA","type":"credit_card","issuer":"visa","card_number_last4":"1111","expire_month":8,"expire_year":2019}},"meta":{"status":1}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $scheduledAt = new DateTime('2018-01-10 12:00');
        $scheduledAt->setTimezone(new DateTimeZone('America/New_York'));

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('4a8fdc1e56261a0b2b2932bd3fb626b9127ae32cd440e9bfa1ad7a7cfce0ddaa');

        $token = (new Structures\Token())
            ->setToken('GMtaX5dzPe3DBUz');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = Factories\ScheduleTransaction::withToken(
            $token,
            $merchant,
            10000,          // Amount
            $scheduledAt,
            Currency::USD,
            $split
        );

        $scheduledTransaction = $sdk->createScheduledTransaction($transaction);

        $this->assertEquals(
            [
                'merchant_id'       => 4,
                'scheduled_at'      => '2018-01-10',
                'timezone'          => 'America/New_York',
                'currency_code'     => 'USD',
                'amount'            => 10000, //amount
                'split_amount'      => 1000,
                'split_merchant_id' => 2,
                'payment_method'    => [
                    'method'    => 'token',
                    'token'     => $token->token()
                ],
            ],
            [
                'merchant_id'       => $scheduledTransaction->merchant()->id(),
                'scheduled_at'      => $scheduledTransaction->scheduledAt()->format('Y-m-d'),
                'timezone'          => $scheduledTransaction->scheduledAt()->getTimezone()->getName(),
                'currency_code'     => $scheduledTransaction->currencyCode(),
                'amount'            => $scheduledTransaction->amount(),
                'split_amount'      => $scheduledTransaction->split()->amount(),
                'split_merchant_id' => $scheduledTransaction->split()->merchant()->id(),
                'payment_method'    => [
                    'method'    => 'token',
                    'token'     => $scheduledTransaction->token()->token()
                ],
            ]
        );

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/scheduled-transactions',
                    'Body' => [
                        'Body' => [
                            'merchant_id'       => 4,
                            'external_id'       => '',
                            'scheduled_at'      => '2018-01-10',
                            'timezone'          => 'America/New_York',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'split_amount'      => 1000,
                            'split_merchant_id' => 2,
                            'payment_method'    => [
                                'method'    => 'token',
                                'token'     => $token->token(),
                            ],
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => '4e8e9634c6aa44222d0c3b206d6b4ef449a92a2e9a2922774c2883a4006370a3'
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => '4e8e9634c6aa44222d0c3b206d6b4ef449a92a2e9a2922774c2883a4006370a3'],
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

        $scheduledAt = new DateTime('2018-01-10 12:00');
        $scheduledAt->setTimezone(new DateTimeZone('America/New_York'));

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('4a8fdc1e56261a0b2b2932bd3fb626b9127ae32cd440e9bfa1ad7a7cfce0ddaa');

        $token = (new Structures\Token())
            ->setToken('InvalidToken123');

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = (new Structures\ScheduledTransaction())
            ->setToken($token)
            ->setMerchant($merchant)
            ->setAmount(10000)
            ->setScheduledAt($scheduledAt)
            ->setCurrencyCode(Currency::USD)
            ->setSplit($split);

        try {
            $scheduledTransaction = $sdk->createScheduledTransaction($transaction);
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
                    'URL'  => 'https://api.mystackpay.com/api/scheduled-transactions',
                    'Body' => [
                        'Body' => [
                            'merchant_id'       => 4,
                            'external_id'       => null,
                            'scheduled_at'      => '2018-01-10',
                            'timezone'          => 'America/New_York',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'split_amount'      => 1000,
                            'split_merchant_id' => 2,
                            'payment_method'    => [
                                'method'    => 'token',
                                'token'     => $token->token()
                            ],
                        ],
                        'Header' => [
                            'Application' => 'PaymentSystem',
                            'ApiVersion'  => 'v1',
                            'Mode'        => 'production',
                            'Security'    => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => hash("sha256",json_encode($sdk::$privateKey.$merchantHash))
                            ]
                        ],
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
