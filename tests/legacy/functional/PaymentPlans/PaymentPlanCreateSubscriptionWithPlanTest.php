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
                            'timezone'          => 'UTC',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'payment_method'    => [
                                'method'        =>  'id',
                                'id'            =>  $subscription->paymentMethod()->id()
                            ],
                        ],
                        [
                            'merchant_id'       => $subscription->paymentPlan()->merchant()->id(),
                            'external_id'       => null,
                            'scheduled_at'      => '2019-02-01',
                            'timezone'          => 'UTC',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'payment_method'    => [
                                'method'        =>  'id',
                                'id'            =>  $subscription->paymentMethod()->id()
                            ],
                        ],
                        [
                            'merchant_id'       => $subscription->paymentPlan()->merchant()->id(),
                            'external_id'       => null,
                            'scheduled_at'      => '2019-03-01',
                            'timezone'          => 'UTC',
                            'currency_code'     => 'USD',
                            'amount'            => 10000, //amount
                            'payment_method'    => [
                                'method'        =>  'id',
                                'id'            =>  $subscription->paymentMethod()->id()
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

        $subscription = $sdk->createPaymentPlanSubscription($subscription);

        $this->assertEquals(
            $respArray['Body'],
            [
                'data' => [
                    'id'                        => $subscription->id(),
                    'initial_transaction'       => [
                        'Status'                => $subscription->initialTransaction()->status(),
                        'Merchant'              => $subscription->initialTransaction()->merchant()->id(),
                        'Order'                 => $subscription->initialTransaction()->order()->id(),
                        'Transaction'           => $subscription->initialTransaction()->id(),
                        'Payment'               => [
                            'Customer'          => $subscription->initialTransaction()->customer()->id(),
                            'PaymentMethod'     => $subscription->initialTransaction()->paymentMethod()->id(),
                            'Amount'            => $subscription->initialTransaction()->amount(),
                            'Currency'          => $subscription->initialTransaction()->currency(),
                            'SplitMerchant'     => $subscription->initialTransaction()->split()->merchant(),
                            'SplitAmount'       => $subscription->initialTransaction()->split()->amount(),
                            'InvoiceNumber'     => $subscription->initialTransaction()->invoiceNumber(),
                            'ExternalId'        => $subscription->initialTransaction()->externalID(),
                            'Comment1'          => $subscription->initialTransaction()->comment1(),
                            'Comment2'          => $subscription->initialTransaction()->comment2(),
                            'AuthorizationCode' => $subscription->initialTransaction()->authCode(),
                            'AVSCode'           => $subscription->initialTransaction()->aVSCode(),
                            'CVVResponseCode'   => $subscription->initialTransaction()->cVVResponseCode(),
                        ],
                        'PaymentMethod'         => [
                            'ID'                => $subscription->initialTransaction()->paymentMethod()->id(),
                            'AccountType'       => $subscription->initialTransaction()->paymentMethod()->account()->type(),
                            'AccountLast4'      => $subscription->initialTransaction()->paymentMethod()->account()->last4(),
                            'ExpirationMonth'   => $subscription->initialTransaction()->paymentMethod()->account()->expireMonth(),
                            'ExpirationYear'    => $subscription->initialTransaction()->paymentMethod()->account()->expireYear(),
                            'BillingName'       => $subscription->initialTransaction()->paymentMethod()->accountHolder()->name(),
                            'BillingAddress'    => [
                                'AddressLine1'  => $subscription->initialTransaction()->paymentMethod()->accountHolder()->billingAddress()->address1(),
                                'AddressLine2'  => $subscription->initialTransaction()->paymentMethod()->accountHolder()->billingAddress()->address2(),
                                'City'          => $subscription->initialTransaction()->paymentMethod()->accountHolder()->billingAddress()->city(),
                                'State'         => $subscription->initialTransaction()->paymentMethod()->accountHolder()->billingAddress()->state(),
                                'Zip'           => $subscription->initialTransaction()->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
                                'Country'       => $subscription->initialTransaction()->paymentMethod()->accountHolder()->billingAddress()->country(),
                            ]
                        ]
                    ],
                    'scheduled_transactions'    => [
                        [
                            'merchant_id'       => $subscription->scheduledTransactions()[0]->merchant()->id(),
                            'external_id'       => $subscription->scheduledTransactions()[0]->externalID(),
                            'scheduled_at'      => $subscription->scheduledTransactions()[0]->scheduledAt()->format('Y-m-d'),
                            'timezone'          => $subscription->scheduledTransactions()[0]->scheduledAt()->getTimezone()->getName(),
                            'currency_code'     => $subscription->scheduledTransactions()[0]->currencyCode(),
                            'amount'            => $subscription->scheduledTransactions()[0]->amount(),
                            'payment_method'    => [
                                'method'        => 'id',
                                'id'            => $subscription->scheduledTransactions()[0]->paymentMethod()->id(),
                            ],
                        ],
                        [
                            'merchant_id'       => $subscription->scheduledTransactions()[1]->merchant()->id(),
                            'external_id'       => $subscription->scheduledTransactions()[1]->externalID(),
                            'scheduled_at'      => $subscription->scheduledTransactions()[1]->scheduledAt()->format('Y-m-d'),
                            'timezone'          => $subscription->scheduledTransactions()[1]->scheduledAt()->getTimezone()->getName(),
                            'currency_code'     => $subscription->scheduledTransactions()[1]->currencyCode(),
                            'amount'            => $subscription->scheduledTransactions()[1]->amount(),
                            'payment_method'    => [
                                'method'        => 'id',
                                'id'            => $subscription->scheduledTransactions()[1]->paymentMethod()->id(),
                            ],
                        ],
                        [
                            'merchant_id'       => $subscription->scheduledTransactions()[2]->merchant()->id(),
                            'external_id'       => $subscription->scheduledTransactions()[2]->externalID(),
                            'scheduled_at'      => $subscription->scheduledTransactions()[2]->scheduledAt()->format('Y-m-d'),
                            'timezone'          => $subscription->scheduledTransactions()[2]->scheduledAt()->getTimezone()->getName(),
                            'currency_code'     => $subscription->scheduledTransactions()[2]->currencyCode(),
                            'amount'            => $subscription->scheduledTransactions()[2]->amount(),
                            'payment_method'    => [
                                'method'        => 'id',
                                'id'            => $subscription->scheduledTransactions()[2]->paymentMethod()->id(),
                            ],
                        ],
                    ],
                ],
            ]
        );
        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/merchants'
                        . '/' . $subscription->paymentPlan()->merchant()->id()
                        . '/payment-plans/' . $subscription->paymentPlan()->id()
                        . '/subscriptions',
                    'Body' => [
                        'Body' => [
                            'payment_method' => [
                                'method' => 'id',
                                'id' => $subscription->paymentPlan()->id(),
                            ],
                            'external_id' => $subscription->externalId(),
                            'amount' =>  $subscription->amount(),
                            'down_payment_amount' => $subscription->downPaymentAmount(),
                            'day' => $subscription->day(),
                            'currency_code' => $subscription->currencyCode(),
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
