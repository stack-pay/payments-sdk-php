<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;
use StackPay\Payments\PaymentPriority;

class PaymentPlansGetByMerchantTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->merchant = new Structures\Merchant(123, 'hashkey_merchant123');
    }

    protected function buildPaymentPlan()
    {
        $paymentPlan                  = new Structures\PaymentPlan;
        $paymentPlan->merchant        = $this->merchant;

        return $paymentPlan;
    }

    public function testGetByMerchant()
    {
        // mock API success response
        $this->mockApiResponse(
            200, 
            [
                'data' => [
                    'id'                  => 12345,
                    'name'                => 'Monthly Plan - 3 Months',
                    'request_incoming_id' => 1111,
                    'down_payment_amount' => 0,
                    'merchant_id'         => 123,
                    'split_merchant_id'   => 124,
                    'payment_priority'    => PaymentPriority::EQUAL,
                    'configuration'       => [
                        'months'          => 3,
                        'day'             => 1,
                    ],
                    'id'                  => 12346,
                    'name'                => 'Monthly Plan - 6 Months',
                    'request_incoming_id' => 1112,
                    'down_payment_amount' => 0,
                    'merchant_id'         => 123,
                    'split_merchant_id'   => 124,
                    'payment_priority'    => PaymentPriority::EQUAL,
                    'configuration'       => [
                        'months'          => 6,
                        'day'             => 1,
                    ],
                ],
                'meta' => [
                    'pagination'          => [
                        'total'           => 3,
                        'count'           => 2,
                        'per_page'        => 2,
                        'current_page'    => 1,
                        'total_pages'     => 2,
                        'links'           => [],
                    ],
                ],
            ],
            $this->merchant->hashKey
        );

        $request = (new Requests\v1\PaymentPlanRequest($this->buildPaymentPlan()))
            ->getMerchantPaymentPlans();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testGetByMerchantWithValidationResponse()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidInputResponse());

        $request = (new Requests\v1\PaymentPlanRequest($this->buildPaymentPlan()))
            ->getMerchantPaymentPlans();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());

        $this->assertEquals($this->response->error()->getCode(), 403);
    }
}
