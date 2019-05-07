<?php

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Exceptions;
use StackPay\Payments\Factories;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class CreateScheduledTransactionWithAccountDetailsTest extends TestCase
{
    public function testWithAccountDetailsFactory()
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
        $scheduledAt->setTimezone(new DateTimeZone('EST'));

        $account = (new Structures\Account())
            ->setType(\StackPay\Payments\AccountTypes::CHECKING)
            ->setNumber('123456')
            ->setRoutingNumber('012367999');

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
            ->setHashKey($merchantHash);

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = Factories\ScheduleTransaction::withAccountDetails(
            $account,
            $accountHolder,
            $merchant,
            10000,      // Amount
            $scheduledAt,
            Currency::USD,
            $split,
            'BSPAY - Scheduled Payment'
        );

        $scheduledTransaction = $sdk->createScheduledTransaction($transaction);

        $this->assertEquals(
            [
                'merchant_id'       => 4,
                'scheduled_at'      => '2018-01-10',
                'timezone'          => 'EST',
                'currency_code'     => 'USD',
                'amount'            => 10000, //amount
                'split_amount'      => 1000,
                'split_merchant_id' => 2,
                'soft_descriptor'   => 'BSPAY - Scheduled Payment',
                'payment_method'    => [
                    'method'            => 'bank_account',
                    'type'              => 'checking',
                    'account_number'    => '123456',
                    'routing_number'    => '012367999',
                    'billing_name'      => 'John Doe',
                    'billing_address_1' => '1234 Windall Lane',
                    'billing_city'      => 'Nowhere',
                    'billing_state'     => 'HI',
                    'billing_zip'       => '89765',
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
                'soft_descriptor'   => $scheduledTransaction->softDescriptor(),
                'payment_method'    => [
                    'method'            => 'bank_account',
                    'type'              => $scheduledTransaction->account()->type(),
                    'account_number'    => $scheduledTransaction->account()->number(),
                    'routing_number'    => $scheduledTransaction->account()->routingNumber,
                    'billing_name'      => $scheduledTransaction->accountHolder()->name(),
                    'billing_address_1' => $scheduledTransaction->accountHolder()->billingAddress()->address1(),
                    'billing_city'      => $scheduledTransaction->accountHolder()->billingAddress()->city(),
                    'billing_state'     => $scheduledTransaction->accountHolder()->billingAddress()->state(),
                    'billing_zip'       => $scheduledTransaction->accountHolder()->billingAddress()->postalCode()
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
                            'timezone'          => 'EST',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'split_amount'      => 1000,
                            'split_merchant_id' => 2,
                            'soft_descriptor'   => 'BSPAY - Scheduled Payment',
                            'payment_method'    => [
                                'method'            => 'bank_account',
                                'type'              => 'checking',
                                'account_number'    => '123456',
                                'routing_number'    => '012367999',
                                'billing_name'      => 'John Doe',
                                'billing_address_1' => '1234 Windall Lane',
                                'billing_city'      => 'Nowhere',
                                'billing_state'     => 'HI',
                                'billing_zip'       => '89765',
                            ],
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

    public function testWithBankAccount()
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
        $scheduledAt->setTimezone(new DateTimeZone('EST'));

        $account = (new Structures\Account())
            ->setType(\StackPay\Payments\AccountTypes::CHECKING)
            ->setNumber('123456')
            ->setRoutingNumber('012367999');

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
            ->setHashKey($merchantHash);

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = (new Structures\ScheduledTransaction())
            ->setAccount($account)
            ->setAccountHolder($accountHolder)
            ->setMerchant($merchant)
            ->setAmount(10000)
            ->setScheduledAt($scheduledAt)
            ->setCurrencyCode(Currency::USD)
            ->setSplit($split)
            ->setSoftDescriptor('BSPAY - Scheduled Payment');


        $scheduledTransaction = $sdk->createScheduledTransaction($transaction);

        $this->assertEquals(
            [
                'merchant_id'       => 4,
                'scheduled_at'      => '2018-01-10',
                'timezone'          => 'EST',
                'currency_code'     => 'USD',
                'amount'            => 10000, //amount
                'split_amount'      => 1000,
                'split_merchant_id' => 2,
                'soft_descriptor'   => 'BSPAY - Scheduled Payment',
                'payment_method'    => [
                    'method'            => 'bank_account',
                    'type'              => 'checking',
                    'account_number'    => '123456',
                    'routing_number'    => '012367999',
                    'billing_name'      => 'John Doe',
                    'billing_address_1' => '1234 Windall Lane',
                    'billing_city'      => 'Nowhere',
                    'billing_state'     => 'HI',
                    'billing_zip'       => '89765',
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
                'soft_descriptor'   => $scheduledTransaction->softDescriptor(),
                'payment_method'    => [
                    'method'            => 'bank_account',
                    'type'              => $scheduledTransaction->account()->type(),
                    'account_number'    => $scheduledTransaction->account()->number(),
                    'routing_number'    => $scheduledTransaction->account()->routingNumber,
                    'billing_name'      => $scheduledTransaction->accountHolder()->name(),
                    'billing_address_1' => $scheduledTransaction->accountHolder()->billingAddress()->address1(),
                    'billing_city'      => $scheduledTransaction->accountHolder()->billingAddress()->city(),
                    'billing_state'     => $scheduledTransaction->accountHolder()->billingAddress()->state(),
                    'billing_zip'       => $scheduledTransaction->accountHolder()->billingAddress()->postalCode()
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
                            'timezone'          => 'EST',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'split_amount'      => 1000,
                            'split_merchant_id' => 2,
                            'soft_descriptor'   => 'BSPAY - Scheduled Payment',
                            'payment_method'    => [
                                'method'            => 'bank_account',
                                'type'              => 'checking',
                                'account_number'    => '123456',
                                'routing_number'    => '012367999',
                                'billing_name'      => 'John Doe',
                                'billing_address_1' => '1234 Windall Lane',
                                'billing_city'      => 'Nowhere',
                                'billing_state'     => 'HI',
                                'billing_zip'       => '89765'
                            ],
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

    public function testWithCreditCard()
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
        $scheduledAt->setTimezone(new DateTimeZone('EST'));

        $account = (new Structures\Account())
            ->setType(\StackPay\Payments\AccountTypes::VISA)
            ->setNumber('4111111111111111')
            ->setExpireMonth('12')
            ->setExpireYear('20')
            ->setCVV2(777);

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
            ->setHashKey($merchantHash);

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = (new Structures\ScheduledTransaction())
            ->setAccount($account)
            ->setAccountHolder($accountHolder)
            ->setMerchant($merchant)
            ->setAmount(10000)
            ->setScheduledAt($scheduledAt)
            ->setCurrencyCode(Currency::USD)
            ->setSplit($split)
            ->setSoftDescriptor('BSPAY - Scheduled Payment');

        $scheduledTransaction = $sdk->createScheduledTransaction($transaction);

        $this->assertEquals(
            [
                'merchant_id'       => 4,
                'scheduled_at'      => '2018-01-10',
                'timezone'          => 'EST',
                'currency_code'     => 'USD',
                'amount'            => 10000, //amount
                'split_amount'      => 1000,
                'split_merchant_id' => 2,
                'soft_descriptor'   => 'BSPAY - Scheduled Payment',
                'payment_method'    => [
                    'method'            => 'credit_card',
                    'type'              => 'visa',
                    'account_number'    => '4111111111111111',
                    'cvv2'              => '777',
                    'expiration_month'  => '12',
                    'expiration_year'   => '20',
                    'billing_name'      => 'John Doe',
                    'billing_address_1' => '1234 Windall Lane',
                    'billing_city'      => 'Nowhere',
                    'billing_state'     => 'HI',
                    'billing_zip'       => '89765',
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
                'soft_descriptor'   => $scheduledTransaction->softDescriptor(),
                'payment_method'    => [
                    'method'            => 'credit_card',
                    'type'              => $scheduledTransaction->account()->type(),
                    'account_number'    => $scheduledTransaction->account()->number(),
                    'cvv2'              => $scheduledTransaction->account()->cvv2(),
                    'expiration_month'  => $scheduledTransaction->account()->expireMonth(),
                    'expiration_year'   => $scheduledTransaction->account()->expireYear(),
                    'billing_name'      => $scheduledTransaction->accountHolder()->name(),
                    'billing_address_1' => $scheduledTransaction->accountHolder()->billingAddress()->address1(),
                    'billing_city'      => $scheduledTransaction->accountHolder()->billingAddress()->city(),
                    'billing_state'     => $scheduledTransaction->accountHolder()->billingAddress()->state(),
                    'billing_zip'       => $scheduledTransaction->accountHolder()->billingAddress()->postalCode()
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
                            'timezone'          => 'EST',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'split_amount'      => 1000,
                            'split_merchant_id' => 2,
                            'soft_descriptor'   => 'BSPAY - Scheduled Payment',
                            'payment_method'    => [
                                'method'            => 'credit_card',
                                'type'              => 'visa',
                                'account_number'    => '4111111111111111',
                                'cvv2'              => 777,
                                'expiration_month'  => '12',
                                'expiration_year'   => '20',
                                'billing_name'      => 'John Doe',
                                'billing_address_1' => '1234 Windall Lane',
                                'billing_city'      => 'Nowhere',
                                'billing_state'     => 'HI',
                                'billing_zip'       => '89765',
                            ],
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
