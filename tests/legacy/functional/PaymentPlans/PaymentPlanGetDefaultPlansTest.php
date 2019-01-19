<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class PaymentPlanGetDefaultPlansTest extends TestCase
{
    public function testSucessfulCase()
    {
        $sdk = new StackPay(
            'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
            '7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
        );

        $plans = (new Structures\MultiplePaymentPlans())
            ->setMerchant((new Structures\Merchant())
                ->setID(1000)
                ->setHashKey('testhashkey')
            );
        $respArray = [
            'Body' => [
                'data' => [
                    [
                        'id'                  => 1000,
                        'name'                => '3-Months Plan',
                        'request_incoming_id' => 1,
                        'down_payment_amount' => 100,
                        'split_merchant_id'   => 2,
                        'merchant_id'         => 1000,
                        'configuration' => [
                            'months'          => 3,
                            'day'             => 15,
                        ],
                        'payment_priority'    => 'equal',
                    ]
                ],
            ],
        ];

        $curlProvider = new MockCurlProvider([[
            'StatusCode' => 200,
            'Body'       => json_encode($respArray),
            'Headers'    => []
        ]]);

        $sdk->setCurlProvider($curlProvider);

        $plans = $sdk->getDefaultPaymentPlans($plans);

        $this->assertEquals(
            $respArray['Body'],
            [
                'data' => [
                    [
                        'id'                  => $plans->plans()[0]->id(),
                        'name'                => $plans->plans()[0]->name(),
                        'request_incoming_id' => $plans->plans()[0]->requestIncomingId(),
                        'down_payment_amount' => $plans->plans()[0]->downPaymentAmount(),
                        'split_merchant_id'   => $plans->plans()[0]->splitMerchant()->id(),
                        'merchant_id'         => $plans->plans()[0]->merchant()->id(),
                        'configuration' => [
                            'months'          => $plans->plans()[0]->configuration()->months(),
                            'day'             => $plans->plans()[0]->configuration()->day(),
                        ],
                        'payment_priority'    => $plans->plans()[0]->paymentPriority(),
                    ]
                ],
            ]
        );

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/payment-plans',
                    'Body' => [
                        'Body' => null,
                        'Header' => [
                            'Application'    => 'PaymentSystem',
                            'ApiVersion'     => 'v1',
                            'Mode'           => 'production',
                        ],
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                        1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                        2 => ['Key' => 'Mode',          'Value' => 'production'],
                        3 => ['Key' => 'Authorization', 'Value' => 'Bearer 7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'],
                        4 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }
}
