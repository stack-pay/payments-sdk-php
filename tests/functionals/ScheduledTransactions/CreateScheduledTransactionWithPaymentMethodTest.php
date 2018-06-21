<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Exceptions;
use StackPay\Payments\Factories;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class CreateScheduledTransactionWithPaymentMethodTest extends TestCase
{
    public function testSuccessfulCase()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"03aa02b3a2cc055dcb2f900ca46ab05b36d664cfecf157762034ff960ba0d0c4"}},"Body":{"data":{"id":154,"merchant_id":4,"scheduled_at":"2018-01-10","currency_code":"USD","amount":10000,"status":"scheduled","split_amount":1000,"split_merchant_id":2,"payment_method":{"id":1,"customer_id":36,"address_1":"5246 Shanon Union Suite 334","city":"South Ian","zip":"25765-4505","address_2":"Apt. 748","state":"NH","country":"USA","type":"credit_card","issuer":"visa","card_number_last4":"509","expire_month":4,"expire_year":2024}},"meta":{"status":1}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $scheduledAt = new DateTime("2018-01-10 12:00");
        $scheduledAt->setTimezone(new DateTimeZone('America/New_York'));

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('4a8fdc1e56261a0b2b2932bd3fb626b9127ae32cd440e9bfa1ad7a7cfce0ddaa');

        $paymentMethod = (new Structures\PaymentMethod())
            ->setID(1);

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = (new Structures\ScheduledTransaction())
            ->setPaymentMethod($paymentMethod)
            ->setMerchant($merchant)
            ->setAmount(10000)
            ->setScheduledAt($scheduledAt)
            ->setCurrencyCode(Currency::USD)
            ->setSplit($split);


        $scheduledTransaction = $sdk->createScheduledTransaction($transaction);

        $this->assertEquals([
            "merchant_id"       => 4,
            "scheduled_at"      => "2018-01-10",
            "timezone"          => "America/New_York",
            "currency_code"     => "USD",
            "amount"            => 10000, //amount
            "split_amount"      => 1000,
            "split_merchant_id" => 2,
            "payment_method"    => [
                "method"    =>  "id",
                "id"        =>  $paymentMethod->id()
            ],
        ],[
            "merchant_id"       => $scheduledTransaction->merchant()->id(),
            "scheduled_at"      => $scheduledTransaction->scheduledAt()->format('Y-m-d'),
            "timezone"          => $scheduledTransaction->scheduledAt()->getTimezone()->getName(),
            "currency_code"     => $scheduledTransaction->currencyCode(),
            "amount"            => $scheduledTransaction->amount(),
            "split_amount"      => $scheduledTransaction->split()->amount(),
            "split_merchant_id" => $scheduledTransaction->split()->merchant()->id(),
            "payment_method"    => [
                "method"    => "id",
                "id"        => $scheduledTransaction->paymentMethod()->id()
            ],
        ]);

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals([
            0 => [
                'URL'  => 'https://api.mystackpay.com/api/scheduled-transactions',
                'Body' => [
                    'Body' => [
                        "merchant_id"       => 4,
                        "scheduled_at"      => "2018-01-10",
                        "timezone"          => "America/New_York",
                        "currency_code"     => "USD",
                        "amount"            => 10000, //amount
                        "split_amount"      => 1000,
                        "split_merchant_id" => 2,
                        "payment_method"    => [
                            "method"    =>  "id",
                            "id"        =>  $paymentMethod->id()
                        ],
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => 'v1',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => 'c27ee02a6292631a26d22cba75812f7fc7fd5674d78cbc07c6359be99cee0e1f'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => 'c27ee02a6292631a26d22cba75812f7fc7fd5674d78cbc07c6359be99cee0e1f'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
        ], $curlProvider->calls );
    }

    public function testInvalidPaymentMethod()
    {

        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"error_code":409,"error_message":"PaymentMethod is invalid or unavailable."}'
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

        $paymentMethod = (new Structures\PaymentMethod())
            ->setID(1);

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = (new Structures\ScheduledTransaction())
            ->setPaymentMethod($paymentMethod)
            ->setMerchant($merchant)
            ->setAmount(10000)
            ->setScheduledAt($scheduledAt)
            ->setCurrencyCode(Currency::USD)
            ->setSplit($split);

        try {
            $scheduledTransaction = $sdk->createScheduledTransaction($transaction);
        } catch (Exceptions\RequestErrorException $e) {
            $this->assertEquals('PaymentMethod is invalid or unavailable.', $e->message());
            $this->assertEquals(409,                                        $e->code());
        } catch (\Exception $e) {
            $this->fail('Unexpected exception thrown: '. $e->getMessage());
        }

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals([
            0 => [
                'URL'  => 'https://api.mystackpay.com/api/scheduled-transactions',
                'Body' => [
                    'Body' => [
                        "merchant_id"       => 4,
                        "scheduled_at"      => "2018-01-10",
                        "timezone"          => "America/New_York",
                        "currency_code"     => "USD",
                        "amount"            => 10000, //amount
                        "split_amount"      => 1000,
                        "split_merchant_id" => 2,
                        "payment_method"    => [
                            "method"    =>  "id",
                            "id"        =>  $paymentMethod->id()
                        ],
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => 'v1',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => 'c27ee02a6292631a26d22cba75812f7fc7fd5674d78cbc07c6359be99cee0e1f'
                        ]
                    ],
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => 'c27ee02a6292631a26d22cba75812f7fc7fd5674d78cbc07c6359be99cee0e1f'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
        ], $curlProvider->calls );
    }

    public function testWithPaymentMethodFactory()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"7235cfc84ccc8203c61ad1c63f807038e1bd704268d6699df3a474eab8c4f869"}},"Body":{"data":{"id":154,"merchant":4,"scheduled_at":"2018-01-10","currency_code":"USD","amount":10000,"status":"scheduled","split_amount":1000,"split_merchant_id":2,"payment_method":{"id":1,"customer_id":36,"address_1":"5246 Shanon Union Suite 334","city":"South Ian","zip":"25765-4505","address_2":"Apt. 748","state":"NH","country":"USA","type":"credit_card","issuer":"visa","card_number_last4":"509","expire_month":4,"expire_year":2024}},"meta":{"status":1}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $scheduledAt = new DateTime("2018-01-10 12:00");
        $scheduledAt->setTimezone(new DateTimeZone('America/New_York'));

        $merchant = (new Structures\Merchant())
            ->setID(4)
            ->setHashKey('4a8fdc1e56261a0b2b2932bd3fb626b9127ae32cd440e9bfa1ad7a7cfce0ddaa');

        $paymentMethod = (new Structures\PaymentMethod())
            ->setID(1);

        $splitMerchant = (new Structures\Merchant())
            ->setID(2);

        $split = (new Structures\Split())
            ->setAmount(1000)
            ->setMerchant($splitMerchant);

        $transaction = Factories\ScheduleTransaction::withPaymentMethod(
            $paymentMethod,
            $merchant,
            10000,          // Amount
            $scheduledAt,
            Currency::USD,
            $split
        );

        $scheduledTransaction = $sdk->createScheduledTransaction($transaction);

        $this->assertEquals([
            "merchant_id"       => 4,
            "scheduled_at"      => "2018-01-10",
            "timezone"          => "America/New_York",
            "currency_code"     => "USD",
            "amount"            => 10000, //amount
            "split_amount"      => 1000,
            "split_merchant_id" => 2,
            "payment_method"    => [
                "method"    =>  "id",
                "id"        =>  $paymentMethod->id()
            ],
        ],[
            "merchant_id"       => $scheduledTransaction->merchant()->id(),
            "scheduled_at"      => $scheduledTransaction->scheduledAt()->format('Y-m-d'),
            "timezone"          => $scheduledTransaction->scheduledAt()->getTimezone()->getName(),
            "currency_code"     => $scheduledTransaction->currencyCode(),
            "amount"            => $scheduledTransaction->amount(),
            "split_amount"      => $scheduledTransaction->split()->amount(),
            "split_merchant_id" => $scheduledTransaction->split()->merchant()->id(),
            "payment_method"    => [
                "method"    => "id",
                "id"        => $scheduledTransaction->paymentMethod()->id()
            ],
        ]);

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals([
            0 => [
                'URL'  => 'https://api.mystackpay.com/api/scheduled-transactions',
                'Body' => [
                    'Body' => [
                        "merchant_id"       => 4,
                        "scheduled_at"      => "2018-01-10",
                        "timezone"          => "America/New_York",
                        "currency_code"     => "USD",
                        "amount"            => 10000, //amount
                        "split_amount"      => 1000,
                        "split_merchant_id" => 2,
                        "payment_method"    => [
                            "method"    =>  "id",
                            "id"        =>  $paymentMethod->id()
                        ],
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => 'v1',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => 'c27ee02a6292631a26d22cba75812f7fc7fd5674d78cbc07c6359be99cee0e1f'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => 'c27ee02a6292631a26d22cba75812f7fc7fd5674d78cbc07c6359be99cee0e1f'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
        ], $curlProvider->calls );
    }
}
