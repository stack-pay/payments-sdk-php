<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class PaymentPlanGetByMerchantTest extends TestCase
{
    public function testSucessfulCase()
    {
        $sdk = new StackPay(
            'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
            '7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
        );

        $merchantHash = 'asdasdasdasd';
        $plans = (new Structures\PaginatedPaymentPlans())
            ->setMerchant((new Structures\Merchant())
                ->setID(1000)
                ->setHashKey($merchantHash)
            )
            ->setPerPage(2)
            ->setCurrentPage(1);
        $respArray = [
            'Body' => [
                'data' => [
                    [
                        'id' => 1000,
                        'name' => '3-Months plan',
                        'request_incoming_id' => 1,
                        'down_payment_amount' => 100,
                        'split_merchant_id' => 2,
                        'merchant_id' => $plans->merchant()->id(),
                        'configuration' => [
                            'months' => 3,
                            'day' => 10
                        ],
                    ],
                    [
                        'id' => 1001,
                        'name' => '6-Months plan',
                        'request_incoming_id' => 1,
                        'down_payment_amount' => 100,
                        'split_merchant_id' => 2,
                        'merchant_id' => $plans->merchant()->id(),
                        'configuration' => [
                            'months' => 6,
                            'day' => 10
                        ],
                    ],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 3,
                        'count' => 2,
                        'per_page' => 2,
                        'current_page' => 1,
                        'total_pages' => 2,
                        'links' => []
                    ],
                ],
            ],
        ];

        $curlProvider = new MockCurlProvider([[
            'StatusCode' => 200,
            'Body'       => json_encode($respArray),
            'Headers'    => []
        ]]);

        $sdk->setCurlProvider($curlProvider);

        $plans = $sdk->getMerchantPaymentPlans($plans);

        $this->assertEquals(
            $respArray['Body'],
            [
                'data' => [
                    [
                        'id' => $plans->plans()[0]->id(),
                        'name' => $plans->plans()[0]->name(),
                        'request_incoming_id' => $plans->plans()[0]->requestIncomingId(),
                        'down_payment_amount' => $plans->plans()[0]->downPaymentAmount(),
                        'split_merchant_id' => $plans->plans()[0]->splitMerchant()->id(),
                        'merchant_id' => $plans->plans()[0]->merchant()->id(),
                        'configuration' => [
                            'months' => $plans->plans()[0]->configuration()->months(),
                            'day' => $plans->plans()[0]->configuration()->day(),
                        ],
                    ],
                    [
                        'id' => $plans->plans()[1]->id(),
                        'name' => $plans->plans()[1]->name(),
                        'request_incoming_id' => $plans->plans()[1]->requestIncomingId(),
                        'down_payment_amount' => $plans->plans()[1]->downPaymentAmount(),
                        'split_merchant_id' => $plans->plans()[1]->splitMerchant()->id(),
                        'merchant_id' => $plans->plans()[1]->merchant()->id(),
                        'configuration' => [
                            'months' => $plans->plans()[1]->configuration()->months(),
                            'day' => $plans->plans()[1]->configuration()->day(),
                        ],
                    ],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => $plans->total(),
                        'count' => $plans->count(),
                        'per_page' => $plans->perPage(),
                        'current_page' => $plans->currentPage(),
                        'total_pages' => $plans->totalPages(),
                        'links' => $plans->links(),
                    ],
                ],
            ]
        );
        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/merchants/' . $plans->merchant()->id() . '/payment-plans?per_page=2&page=1',
                    'Body' => [
                        'Body' => null,
                        'Header' => [
                            'Application'    => 'PaymentSystem',
                            'ApiVersion'     => 'v1',
                            'Mode'           => 'production',
                            'Security'       => [
                                'HashMethod' => 'SHA-256',
                                'Hash'       => hash('sha256', $sdk::$privateKey . $merchantHash),
                            ],
                        ],],
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
