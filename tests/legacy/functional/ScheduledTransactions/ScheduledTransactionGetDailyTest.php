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
                    }
                ],
                "meta": {
                    "pagination": {
                        "total": 372,
                        "count": 1,
                        "per_page": 1,
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
            ->setBeforeDate(new DateTime('2016-01-01', new DateTimeZone('EST')))
            ->setAfterDate(new DateTime('2016-01-01', new DateTimeZone('EST')));

        $transaction = $sdk->getDailyScheduledTransactions($transaction);

        $respArray = json_decode($respArray, true);
        
        $this->assertEquals(
			$respArray['Body']['data'],
			[
					[
						"id"							=> $transaction->scheduledTransactions()[0]->id(),
						"merchant_id"					=> $transaction->scheduledTransactions()[0]->merchant()->id(),
						"payment_method_id"				=> $transaction->scheduledTransactions()[0]->paymentMethod()->id(),
						"external_id"					=> $transaction->scheduledTransactions()[0]->externalId(),
						"scheduled_at"					=> $transaction->scheduledTransactions()[0]->scheduledAt()->format('Y-m-d'),
						"status"						=> $transaction->scheduledTransactions()[0]->status(),
						"currency_code"					=> $transaction->scheduledTransactions()[0]->currencyCode(),
						"amount"						=> $transaction->scheduledTransactions()[0]->amount(),
						"split_amount"					=> ($transaction->scheduledTransactions()[0]->split() ? $transaction->scheduledTransactions()[0]->split()->amount() : null),
						"split_merchant_id"				=> ($transaction->scheduledTransactions()[0]->split() ? $transaction->scheduledTransactions()[0]->split()->merchant()->id() : null),
						"subscription"					=> $transaction->scheduledTransactions()[0]->subscriptionId(),
						"payment_method"				=> [

									"method"					=> $transaction->scheduledTransactions()[0]->paymentMethod()->method(),
									"id"						=> $transaction->scheduledTransactions()[0]->paymentMethod()->id(),
									"customer_id"				=> $transaction->scheduledTransactions()[0]->paymentMethod()->customer()->id(),
									"type"						=> $transaction->scheduledTransactions()[0]->paymentMethod()->account()->type(),
									"routing_last_four" 		=> $transaction->scheduledTransactions()[0]->paymentMethod()->account()->routingLast4(),
									"account_last_four"			=> $transaction->scheduledTransactions()[0]->paymentMethod()->account()->last4(),
									"expiration_month"			=> $transaction->scheduledTransactions()[0]->paymentMethod()->account()->expireMonth(),
									"expiration_year"			=> $transaction->scheduledTransactions()[0]->paymentMethod()->account()->expireYear(),
									"billing_name"				=> $transaction->scheduledTransactions()[0]->paymentMethod()->accountHolder()->name(),
									"billing_address_1"			=> $transaction->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->address1(),
									"billing_address_2"			=> $transaction->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->address2(),
									"billing_city"				=> $transaction->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->city(),
									"billing_zip"				=> $transaction->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
									"billing_state"				=> $transaction->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->state(),
									"billing_country"			=> $transaction->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->country(),
									"customer"					=> [

											"id"			=> $transaction->scheduledTransactions()[0]->paymentMethod()->customer()->id(),
											"first_name"	=> $transaction->scheduledTransactions()[0]->paymentMethod()->customer()->firstName(),
											"last_name"		=> $transaction->scheduledTransactions()[0]->paymentMethod()->customer()->lastName()
									]

						],
						"transactions" 					=> []
					]
			]
		);

    }
}
