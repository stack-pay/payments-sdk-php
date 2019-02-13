<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;
use StackPay\Payments\PaymentPriority;

class PaymentPlanEditSubscriptionTest extends FunctionalTestCase
{
	public function setUp()
	{
		parent::setUp();

		$this->merchant = new Structures\Merchant(123, 'hashkey_merchant123');
		$this->splitMerchant = new Structures\Merchant(124, 'hashkey_merchant124');
	}

	protected function buildPaymentPlanSubscription()
	{
		$paymentPlan                     = new Structures\PaymentPlan();
		$paymentPlan->id                 = 123;
		$paymentPlan->merchant           = $this->merchant;
		$paymentPlan->splitMerchant      = $this->splitMerchant;
		$paymentPlan->paymentPriority    = PaymentPriority::EQUAL;
		$paymentMethod                   = new Structures\PaymentMethod();
		$paymentMethod->id               = 123;

		$subscription                    = new Structures\Subscription();
		$subscription->id				 = 123;
		$subscription->externalID        = '1000';
		$subscription->amount            = 15000;
		$subscription->downPaymentAmount = 5000;
		$subscription->day               = 10;
		$subscription->currencyCode      = 'USD';

		$subscription->paymentMethod     = $paymentMethod;
		$subscription->paymentPlan       = $paymentPlan;

		return $subscription;
	}

	public function testEditSubscription()
	{
		$subscription = $this->buildPaymentPlanSubscription();

		$subscription->paymentMethod->id = 999;
		$subscription->amount = 20000;
		$subscription->splitAmount = 10000;

		// mock API success response
		$this->mockApiResponse(
			200,
			[
				'data' => [
					'id' => 1,
					'down_payment_transaction' => [
						'id' => 8445,
						'created_at' => '2019-01-23 01:33:51',
						'status' => 1,
						'order_id' => 7660,
						'original_transaction_id' => null,
						'payment_method_id' => 8068,
						'type' => 'process',
						'external_id' => null,
						'invoice_number' => null,
						'amount' => 20000,
						'split_merchant_id' => null,
						'split_amount' => 10000,
						'fee_rate' => 3.65,
						'fee_flat' => 30,
						'fee_total' => 213,
						'error' => null,
						'payment_method' => [
							'method' => 'credit_card',
							'id' => 8068,
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
							'amount' => 6666,
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
							'amount' => 6666,
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
							'amount' => 6668,
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
				],
			],
			$this->merchant->hashKey
		);

		$request = (new Requests\v1\PaymentPlanRequest(null, $subscription))
			->editPaymentPlanSubscription();

		$this->response = $request->send();

		$this->assertResourceResponse();
	}

	public function testEditSubscriptionWithValidationResponse()
	{
		// mock API success response
		$this->mockApiResponse(422, $this->invalidInputResponse());

		$subscription = $this->buildPaymentPlanSubscription();

		$request = (new Requests\v1\PaymentPlanRequest(null, $subscription))
			->editPaymentPlanSubscription();

		$this->response = $request->send();

		$this->assertEquals($this->response->status(), 422);
		$this->assertNotTrue($this->response->success());
		$this->assertNotNull($this->response->error());

		$this->assertEquals($this->response->error()->getCode(), 403);
	}

	public function testEditSubscriptionWithInValidPaymentMethod()
	{
		// mock API success response
		$this->mockApiResponse(422, $this->invalidPaymentMethodResponse());

		$subscription = $this->buildPaymentPlanSubscription();

		$request = (new Requests\v1\PaymentPlanRequest(null, $subscription))
			->editPaymentPlanSubscription();

		$this->response = $request->send();

		$this->assertEquals($this->response->status(), 422);
		$this->assertNotTrue($this->response->success());
		$this->assertNotNull($this->response->error());

		$this->assertEquals($this->response->error()->getCode(), 409);
	}
}
