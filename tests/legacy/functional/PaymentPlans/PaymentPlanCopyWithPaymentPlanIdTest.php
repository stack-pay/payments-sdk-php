<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class PaymentPlanCopyWithPaymentPlanIdTest extends TestCase
{
    public function testSucessfulCase()
    {
        $sdk = new StackPay(
            'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
            '7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
        );

        $merchantHash = 'asdasdasdasd';
        $plan = (new Structures\PaymentPlan())
            ->setID(1000)
            ->setMerchant((new Structures\Merchant())
                ->setID(1000)
                ->setHashKey($merchantHash)
            );
        $respArray = [
            'Body' => [
                'data' => [
                    'id'                  => 1001,
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
                ],
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
