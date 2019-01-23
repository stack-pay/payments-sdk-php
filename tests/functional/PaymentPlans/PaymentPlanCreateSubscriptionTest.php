<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;
use StackPay\Payments\PaymentPriority;

class PaymentPlanCreateSubscriptionTest extends FunctionalTestCase
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
        $subscription->externalID        = '1000';
        $subscription->amount            = 15000;
        $subscription->downPaymentAmount = 5000;
        $subscription->day               = 10;
        $subscription->currencyCode      = 'USD';

        $subscription->paymentMethod     = $paymentMethod;
        $subscription->paymentPlan       = $paymentPlan;

        return $subscription;
    }

    public function testCreate()
    {
        $subscription = $this->buildPaymentPlanSubscription();

        // mock API success response
        $this->mockApiResponse(
            200, 
            [
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
                ],
            ],
            $this->merchant->hashKey
        );

        $request = (new Requests\v1\PaymentPlanRequest(null, $subscription))
            ->createPaymentPlanSubscription();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testCreateWithValidationResponse()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidInputResponse());

        $request = (new Requests\v1\PaymentPlanRequest(null, $this->buildPaymentPlanSubscription()))
            ->createPaymentPlanSubscription();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());

        $this->assertEquals($this->response->error()->getCode(), 403);
    }
}
