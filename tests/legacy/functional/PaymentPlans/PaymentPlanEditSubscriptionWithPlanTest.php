<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\Currency;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class PaymentPlanEditSubscriptionWithPlanTest extends TestCase
{
	public function testSuccessfulCase()
	{
		$sdk = new StackPay(
			'e3dc59dca5c8f080c8d1b67eb3bcf1e1b73bff0bace6d7bcedc2e756997bd07d',
			'7b986b7a09affd0d7bcb13214f5856b40f444858d728e5457931c82eea3d233c'
		);

		$merchantHash = 'asdasdasdasd';
		$account = (new Structures\Account())
			->setType('visa')
			->setLast4('1111')
			->setExpireMonth(1)
			->setExpireYear(2020);

		$address = (new Structures\Address())
			->setAddress1('123 Test Ln')
			->setCity('StackVille')
			->setState('TX')
			->setPostalCode('01234')
			->setCountry('USA');

		$accountHolder = (new Structures\AccountHolder())
			->setName('Stack Testa')
			->setBillingAddress($address);

		$customer = (new Structures\Customer())
			->setID(8133);

		$subscription = (new Structures\Subscription())
			->setPaymentPlan((new Structures\PaymentPlan())
				->setID(1000)
				->setMerchant((new Structures\Merchant())
					->setID(1000)
					->setHashKey($merchantHash)
				)
			)
			->setPaymentMethod((new Structures\PaymentMethod())
				->setID(999)
				->setAccount($account)
				->setAccountHolder($accountHolder)
				->setCustomer($customer)
			)
			->setExternalId('1000')
			->setAmount(20000)
			->setSplitAmount(10000)
			->setDownPaymentAmount(1500)
			->setDay(1);

		$respArray = [
			'Body' => [
				'data' => [
					'id' => 1,
					'down_payment_transaction' => [
						'id' => 8445,
						'created_at' => '2019-01-23 01:33:51',
						'status' => 1,
						'order_id' => 7660,
						'original_transaction_id' => null,
						'payment_method_id' => 999,
						'type' => 'process',
						'external_id' => null,
						'invoice_number' => null,
						'amount' => 5000,
						'split_merchant_id' => null,
						'split_amount' => null,
						'fee_rate' => 3.65,
						'fee_flat' => 30,
						'fee_total' => 213,
						'error' => null,
						'payment_method' => [
							'method' => 'credit_card',
							'id' => 999,
							'customer_id' => 8133,
							'type' => 'visa',
							'routing_last_four' => null,
							'account_last_four' => '1111',
							'expiration_month' => 1,
							'expiration_year' => 2020,
							'billing_name' => 'Stack Testa',
							'billing_address_1' => '123 Test Ln',
							'billing_address_2' => null,
							'billing_city' => 'StackVille',
							'billing_zip' => '01234',
							'billing_state' => 'TX',
							'billing_country' => 'USA',
							'customer' => [
								'id' => 8133,
								'first_name' => 'Stack',
								'last_name' => 'Testa'
							]
						]
					],
					'scheduled_transactions' => [
						[
							'id' => 228,
							'merchant_id' => 1000,
							'payment_method_id' => 999,
							'external_id' => null,
							'scheduled_at' => '2019-02-23',
							'status' => 'scheduled',
							'currency_code' => 'USD',
							'amount' => 1668,
							'split_amount' => null,
							'split_merchant_id' => null,
							'subscription' => 1,
							'payment_method' => [
								'method' => 'credit_card',
								'id' => 999,
								'customer_id' => 8133,
								'type' => 'visa',
								'routing_last_four' => null,
								'account_last_four' => '1111',
								'expiration_month' => 1,
								'expiration_year' => 2020,
								'billing_name' => 'Stack Testa',
								'billing_address_1' => '123 Test Ln',
								'billing_address_2' => null,
								'billing_city' => 'StackVille',
								'billing_zip' => '01234',
								'billing_state' => 'TX',
								'billing_country' => 'USA',
								'customer' => [
									'id' => 8133,
									'first_name' => 'Stack',
									'last_name' => 'Testa'
								]
							],
							'transactions' => []
						],
						[
							'id' => 229,
							'merchant_id' => 1000,
							'payment_method_id' => 999,
							'external_id' => null,
							'scheduled_at' => '2019-03-23',
							'status' => 'scheduled',
							'currency_code' => 'USD',
							'amount' => 1668,
							'split_amount' => null,
							'split_merchant_id' => null,
							'subscription' => 1,
							'payment_method' => [
								'method' => 'credit_card',
								'id' => 999,
								'customer_id' => 8133,
								'type' => 'visa',
								'routing_last_four' => null,
								'account_last_four' => '1111',
								'expiration_month' => 1,
								'expiration_year' => 2020,
								'billing_name' => 'Stack Testa',
								'billing_address_1' => '123 Test Ln',
								'billing_address_2' => null,
								'billing_city' => 'StackVille',
								'billing_zip' => '01234',
								'billing_state' => 'TX',
								'billing_country' => 'USA',
								'customer' => [
									'id' => 8133,
									'first_name' => 'Stack',
									'last_name' => 'Testa'
								]
							],
							'transactions' => []
						],
						[
							'id' => 230,
							'merchant_id' => 1000,
							'payment_method_id' => 999,
							'external_id' => null,
							'scheduled_at' => '2019-04-23',
							'status' => 'scheduled',
							'currency_code' => 'USD',
							'amount' => 1668,
							'split_amount' => null,
							'split_merchant_id' => null,
							'subscription' => 1,
							'payment_method' => [
								'method' => 'credit_card',
								'id' => 999,
								'customer_id' => 8133,
								'type' => 'visa',
								'routing_last_four' => null,
								'account_last_four' => '1111',
								'expiration_month' => 1,
								'expiration_year' => 2020,
								'billing_name' => 'Stack Testa',
								'billing_address_1' => '123 Test Ln',
								'billing_address_2' => null,
								'billing_city' => 'StackVille',
								'billing_zip' => '01234',
								'billing_state' => 'TX',
								'billing_country' => 'USA',
								'customer' => [
									'id' => 8133,
									'first_name' => 'Stack',
									'last_name' => 'Testa'
								]
							],
							'transactions' => []
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

		$subscription = $sdk->editPaymentPlanSubscription($subscription);

		$this->assertEquals(
			$respArray['Body'],
			[
				'data' => [
					'id'                        => $subscription->id(),
					'down_payment_transaction'       => [
						'id' => $subscription->downPaymentTransaction()->id(),
						'created_at' => '2019-01-23 01:33:51',
						'status' => $subscription->downPaymentTransaction()->status(),
						'order_id' => $subscription->downPaymentTransaction()->order()->id(),
						'original_transaction_id' => null,
						'payment_method_id' => $subscription->downPaymentTransaction()->paymentMethod()->id(),
						'type' => 'process',
						'external_id' => null,
						'invoice_number' => null,
						'amount' => $subscription->downPaymentTransaction()->amount(),
						'split_merchant_id' => null,
						'split_amount' => null,
						'fee_rate' => 3.65,
						'fee_flat' => 30,
						'fee_total' => 213,
						'error' => null,
						'payment_method' => [
							'method' => 'credit_card',
							'id' => $subscription->downPaymentTransaction()->paymentMethod()->id(),
							'customer_id' => $subscription->downPaymentTransaction()->customer()->id(),
							'type' => $subscription->downPaymentTransaction()->paymentMethod()->account()->type(),
							'routing_last_four' => null,
							'account_last_four' => $subscription->downPaymentTransaction()->paymentMethod()->account()->last4(),
							'expiration_month' => $subscription->downPaymentTransaction()->paymentMethod()->account()->expireMonth(),
							'expiration_year' => $subscription->downPaymentTransaction()->paymentMethod()->account()->expireYear(),
							'billing_name' => $subscription->downPaymentTransaction()->paymentMethod()->accountHolder()->name(),
							'billing_address_1' => $subscription->downPaymentTransaction()->paymentMethod()->accountHolder()->billingAddress()->address1(),
							'billing_address_2' => $subscription->downPaymentTransaction()->paymentMethod()->accountHolder()->billingAddress()->address2(),
							'billing_city' => $subscription->downPaymentTransaction()->paymentMethod()->accountHolder()->billingAddress()->city(),
							'billing_zip' => $subscription->downPaymentTransaction()->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
							'billing_state' => $subscription->downPaymentTransaction()->paymentMethod()->accountHolder()->billingAddress()->state(),
							'billing_country' => $subscription->downPaymentTransaction()->paymentMethod()->accountHolder()->billingAddress()->country(),
							'customer' => [
								'id' => $subscription->downPaymentTransaction()->customer()->id(),
								'first_name' => 'Stack',
								'last_name' => 'Testa'
							],
						],
					],
					'scheduled_transactions'    => [
						[
							'id'                    => $subscription->scheduledTransactions()[0]->id(),
							'merchant_id'           => $subscription->scheduledTransactions()[0]->merchant()->id(),
							'payment_method_id'     => $subscription->scheduledTransactions()[0]->paymentMethod()->id(),
							'external_id'           => $subscription->scheduledTransactions()[0]->externalID(),
							'scheduled_at'          => $subscription->scheduledTransactions()[0]->scheduledAt()->format('Y-m-d'),
							'status'                => 'scheduled',
							'currency_code'         => $subscription->scheduledTransactions()[0]->currencyCode(),
							'amount'                => $subscription->scheduledTransactions()[0]->amount(),
							'split_amount'          => null,
							'split_merchant_id'     => null,
							'subscription'          => 1,
							'payment_method'        => [
								'method'            => 'credit_card',
								'id'                => $subscription->scheduledTransactions()[0]->paymentMethod()->id(),
								'customer_id'       => 8133,
								'type'              => $subscription->scheduledTransactions()[0]->paymentMethod()->account()->type(),
								'routing_last_four' => null,
								'account_last_four' => $subscription->scheduledTransactions()[0]->paymentMethod()->account()->last4(),
								'expiration_month'  => $subscription->scheduledTransactions()[0]->paymentMethod()->account()->expireMonth(),
								'expiration_year'   => $subscription->scheduledTransactions()[0]->paymentMethod()->account()->expireYear(),
								'billing_name'      => $subscription->scheduledTransactions()[0]->paymentMethod()->accountHolder()->name(),
								'billing_address_1' => $subscription->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->address1(),
								'billing_address_2' => $subscription->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->address2(),
								'billing_city'      => $subscription->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->city(),
								'billing_zip'       => $subscription->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
								'billing_state'     => $subscription->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->state(),
								'billing_country'   => $subscription->scheduledTransactions()[0]->paymentMethod()->accountHolder()->billingAddress()->country(),
								'customer'          => [
									'id'            => 8133,
									'first_name'    => 'Stack',
									'last_name'     => 'Testa'
								]
							],
							'transactions' => []
						],
						[
							'id'                    => $subscription->scheduledTransactions()[1]->id(),
							'merchant_id'           => $subscription->scheduledTransactions()[1]->merchant()->id(),
							'payment_method_id'     => $subscription->scheduledTransactions()[1]->paymentMethod()->id(),
							'external_id'           => $subscription->scheduledTransactions()[1]->externalID(),
							'scheduled_at'          => $subscription->scheduledTransactions()[1]->scheduledAt()->format('Y-m-d'),
							'status'                => 'scheduled',
							'currency_code'         => $subscription->scheduledTransactions()[1]->currencyCode(),
							'amount'                => $subscription->scheduledTransactions()[1]->amount(),
							'split_amount'          => null,
							'split_merchant_id'     => null,
							'subscription'          => 1,
							'payment_method'        => [
								'method'            => 'credit_card',
								'id'                => $subscription->scheduledTransactions()[1]->paymentMethod()->id(),
								'customer_id'       => 8133,
								'type'              => $subscription->scheduledTransactions()[1]->paymentMethod()->account()->type(),
								'routing_last_four' => null,
								'account_last_four' => $subscription->scheduledTransactions()[1]->paymentMethod()->account()->last4(),
								'expiration_month'  => $subscription->scheduledTransactions()[1]->paymentMethod()->account()->expireMonth(),
								'expiration_year'   => $subscription->scheduledTransactions()[1]->paymentMethod()->account()->expireYear(),
								'billing_name'      => $subscription->scheduledTransactions()[1]->paymentMethod()->accountHolder()->name(),
								'billing_address_1' => $subscription->scheduledTransactions()[1]->paymentMethod()->accountHolder()->billingAddress()->address1(),
								'billing_address_2' => $subscription->scheduledTransactions()[1]->paymentMethod()->accountHolder()->billingAddress()->address2(),
								'billing_city'      => $subscription->scheduledTransactions()[1]->paymentMethod()->accountHolder()->billingAddress()->city(),
								'billing_zip'       => $subscription->scheduledTransactions()[1]->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
								'billing_state'     => $subscription->scheduledTransactions()[1]->paymentMethod()->accountHolder()->billingAddress()->state(),
								'billing_country'   => $subscription->scheduledTransactions()[1]->paymentMethod()->accountHolder()->billingAddress()->country(),
								'customer'          => [
									'id'            => 8133,
									'first_name'    => 'Stack',
									'last_name'     => 'Testa'
								]
							],
							'transactions' => []
						],
						[
							'id'                    => $subscription->scheduledTransactions()[2]->id(),
							'merchant_id'           => $subscription->scheduledTransactions()[2]->merchant()->id(),
							'payment_method_id'     => $subscription->scheduledTransactions()[2]->paymentMethod()->id(),
							'external_id'           => $subscription->scheduledTransactions()[2]->externalID(),
							'scheduled_at'          => $subscription->scheduledTransactions()[2]->scheduledAt()->format('Y-m-d'),
							'status'                => 'scheduled',
							'currency_code'         => $subscription->scheduledTransactions()[2]->currencyCode(),
							'amount'                => $subscription->scheduledTransactions()[2]->amount(),
							'split_amount'          => null,
							'split_merchant_id'     => null,
							'subscription'          => 1,
							'payment_method'        => [
								'method'            => 'credit_card',
								'id'                => $subscription->scheduledTransactions()[2]->paymentMethod()->id(),
								'customer_id'       => 8133,
								'type'              => $subscription->scheduledTransactions()[2]->paymentMethod()->account()->type(),
								'routing_last_four' => null,
								'account_last_four' => $subscription->scheduledTransactions()[2]->paymentMethod()->account()->last4(),
								'expiration_month'  => $subscription->scheduledTransactions()[2]->paymentMethod()->account()->expireMonth(),
								'expiration_year'   => $subscription->scheduledTransactions()[2]->paymentMethod()->account()->expireYear(),
								'billing_name'      => $subscription->scheduledTransactions()[2]->paymentMethod()->accountHolder()->name(),
								'billing_address_1' => $subscription->scheduledTransactions()[2]->paymentMethod()->accountHolder()->billingAddress()->address1(),
								'billing_address_2' => $subscription->scheduledTransactions()[2]->paymentMethod()->accountHolder()->billingAddress()->address2(),
								'billing_city'      => $subscription->scheduledTransactions()[2]->paymentMethod()->accountHolder()->billingAddress()->city(),
								'billing_zip'       => $subscription->scheduledTransactions()[2]->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
								'billing_state'     => $subscription->scheduledTransactions()[2]->paymentMethod()->accountHolder()->billingAddress()->state(),
								'billing_country'   => $subscription->scheduledTransactions()[2]->paymentMethod()->accountHolder()->billingAddress()->country(),
								'customer'          => [
									'id'            => 8133,
									'first_name'    => 'Stack',
									'last_name'     => 'Testa'
								]
							],
							'transactions' => []
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
						. '/subscriptions/',
					'Body' => [
						'Body' => [
							'payment_method' => [
								'method' => 'id',
								'id' => $subscription->paymentMethod()->id(),
							],
							'amount' => $subscription->amount(),
							'split_amount' => $subscription->splitAmount()
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
