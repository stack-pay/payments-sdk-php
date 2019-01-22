<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class PaymentPlanCreateSubscriptionWithPlanTest extends TestCase
{
    public function testSucessfulCase()
    {
        $sdk = new StackPay(
            'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
            '7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
        );

        $merchantHash = 'asdasdasdasd';
        $subscription = (new Structures\Subscription())
            ->setPaymentPlan((new Structures\PaymentPlan())
                ->setID(1000)
                ->setMerchant((new Structures\Merchant())
                    ->setID(1000)
                    ->setHashKey($merchantHash)
                )
            )
            ->setPaymentMethod((new Structures\PaymentMethod())
                ->setID(1000)
            )
            ->setExternalId('1000')
            ->setAmount(20000)
            ->setDownPaymentAmount(1500)
            ->setDay(1);
        $respArray = [
            'Body' => [
                'data' => [
                    'id' => 1,
                    'initial_transaction' => [
                        'Status' => 1,
                        'Merchant' => 107,
                        'Order' => 356483,
                        'Transaction' => 373316,
                        'Payment' => [
                            'Customer' => 249579,
                            'PaymentMethod' => 245341,
                            'Amount' => 16450,
                            'Currency' => 'USD',
                            'SplitMerchant' => null,
                            'SplitAmount' => null,
                            'InvoiceNumber' => null,
                            'ExternalId' => null,
                            'Comment1' => null,
                            'Comment2' => null,
                            'AuthorizationCode' => '08738C',
                            'AVSCode' => 'Y',
                            'CVVResponseCode' => 'NotPresent'
                        ],
                        'PaymentMethod' => [
                            'ID' => 245341,
                            'AccountType' => 'visa',
                            'AccountLast4' => '6637',
                            'ExpirationMonth' => 5,
                            'ExpirationYear' => 2023,
                            'BillingName' => 'Chris Meyers',
                            'BillingAddress' => [
                                'AddressLine1' => '35 Chippen Hill Dr.',
                                'AddressLine2' => '',
                                'City' => 'Kensington',
                                'State' => 'CT',
                                'Zip' => '06037',
                                'Country' => 'USA'
                            ]
                        ]
                    ],
                    'scheduled_transactions' => [
                        [
                            'merchant_id'       => $subscription->paymentPlan()->merchant()->id(),
                            'external_id'       => null,
                            'scheduled_at'      => '2019-01-01',
                            'timezone'          => 'America/New_York',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'payment_method'    => [
                                'method'    =>  'id',
                                'id'        =>  $subscription->paymentMethod()->id()
                            ],
                        ],
                        [
                            'merchant_id'       => $subscription->paymentPlan()->merchant()->id(),
                            'external_id'       => null,
                            'scheduled_at'      => '2019-02-01',
                            'timezone'          => 'America/New_York',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'payment_method'    => [
                                'method'    =>  'id',
                                'id'        =>  $subscription->paymentMethod()->id()
                            ],
                        ],
                        [
                            'merchant_id'       => $subscription->paymentPlan()->merchant()->id(),
                            'external_id'       => null,
                            'scheduled_at'      => '2019-03-01',
                            'timezone'          => 'America/New_York',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'payment_method'    => [
                                'method'    =>  'id',
                                'id'        =>  $subscription->paymentMethod()->id()
                            ],
                        ]
                    ]
                ]
            ],
        ];

        $curlProvider = new MockCurlProvider([[
            'StatusCode' => 200,
            'Body'       => json_encode($respArray),
            'Headers'    => []
        ]]);

        $sdk->setCurlProvider($curlProvider);

        $plan = $sdk->copyPaymentPlan($plan);

        $this->assertEquals(
            $respArray['Body'],
            [
                'data' => [
                    'id'                  => $plan->id(),
                    'name'                => $plan->name(),
                    'request_incoming_id' => $plan->requestIncomingId(),
                    'down_payment_amount' => $plan->downPaymentAmount(),
                    'split_merchant_id'   => $plan->splitMerchant()->id(),
                    'merchant_id'         => $plan->merchant()->id(),
                    'configuration' => [
                        'months'          => $plan->configuration()->months(),
                        'day'             => $plan->configuration()->day(),
                    ],
                    'payment_priority'    => $plan->paymentPriority(),
                ],
            ]
        );
        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/merchants/1000/payment-plans',
                    'Body' => [
                        'Body' => [
                            'payment_plan_id'     => 1000,
                            'merchant_id'         => 1000,
                        ],
                        'Header' => [
                            'Application'    => 'PaymentSystem',
                            'ApiVersion'     => 'v1',
                            'Mode'           => 'production',
                            'Security'       => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => hash('sha256', $sdk::$privateKey . $merchantHash),
                            ],
                        ],
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                        4 => ['Key' => 'Hash',          'Value' => hash('sha256', $sdk::$privateKey . $merchantHash)],
                        5 => ['Key' => 'Authorization', 'Value' => 'Bearer 7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'],
                        6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }
}
