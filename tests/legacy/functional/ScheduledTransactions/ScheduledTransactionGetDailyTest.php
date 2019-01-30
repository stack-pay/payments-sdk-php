<?php

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Exceptions;
use StackPay\Payments\Factories;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class GetDailyScheduledTransactionsTest extends TestCase
{
    public function testGetDailyScheduledTransactions()
    {
        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $respArray = '{
            "Header": {
                "Security": {
                    "HashMethod": "SHA-256",
                    "Hash": "6f6088b877414b0e5b9009c3577a8269e25113c69b2daf737cd5b8d347848a10"
                }
            },
            "Body": {
                "data": [
                    {
                        "id": 261,
                        "merchant_id": 3063,
                        "payment_method_id": 8068,
                        "external_id": null,
                        "scheduled_at": "2019-02-23",
                        "status": "scheduled",
                        "currency_code": "USD",
                        "amount": 135,
                        "split_amount": null,
                        "split_merchant_id": null,
                        "subscription": 11,
                        "payment_method": {
                            "method": "credit_card",
                            "id": 8068,
                            "customer_id": 8133,
                            "type": "visa",
                            "routing_last_four": null,
                            "account_last_four": "1111",
                            "expiration_month": 1,
                            "expiration_year": 2020,
                            "billing_name": "Stack Testa",
                            "billing_address_1": "123 Test Ln",
                            "billing_address_2": null,
                            "billing_city": "StackVille",
                            "billing_zip": "01234",
                            "billing_state": "TX",
                            "billing_country": "USA",
                            "customer": {
                                "id": 8133,
                                "first_name": "Stack",
                                "last_name": "Testa"
                            }
                        },
                        "transactions": []
                    },
                    {
                        "id": 262,
                        "merchant_id": 3063,
                        "payment_method_id": 8068,
                        "external_id": null,
                        "scheduled_at": "2019-03-23",
                        "status": "scheduled",
                        "currency_code": "USD",
                        "amount": 135,
                        "split_amount": null,
                        "split_merchant_id": null,
                        "subscription": 11,
                        "payment_method": {
                            "method": "credit_card",
                            "id": 8068,
                            "customer_id": 8133,
                            "type": "visa",
                            "routing_last_four": null,
                            "account_last_four": "1111",
                            "expiration_month": 1,
                            "expiration_year": 2020,
                            "billing_name": "Stack Testa",
                            "billing_address_1": "123 Test Ln",
                            "billing_address_2": null,
                            "billing_city": "StackVille",
                            "billing_zip": "01234",
                            "billing_state": "TX",
                            "billing_country": "USA",
                            "customer": {
                                "id": 8133,
                                "first_name": "Stack",
                                "last_name": "Testa"
                            }
                        },
                        "transactions": []
                    }
                ],
                "meta": {
                    "pagination": {
                        "total": 372,
                        "count": 2,
                        "per_page": 2,
                        "current_page": 1,
                        "total_pages": 186,
                        "links": {
                            "next": "https://staging-api.stackpay.com/api/scheduled-transactions?page=2"
                        }
                    }
                }
            }
        }';

        $curlProvider = new MockCurlProvider([[
           'StatusCode' => 200,
           'Body'       => $respArray,
           'Headers'    => []
        ]]);

        $sdk->setCurlProvider($curlProvider);

        // set paginatedScheduledTransaction details
        $transaction = (new Structures\PaginatedScheduledTransactions())
            ->setBeforeDate(new DateTime('2016-01-01', new DateTimeZone('America/New_York')))
            ->setAfterDate(new DateTime('2016-01-01', new DateTimeZone('America/New_York')));

        $transaction = $sdk->getDailyScheduledTransactions($transaction);

        var_dump($transaction);

    }
}
