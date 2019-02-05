<?php

use StackPay\Payments\Requests\v1\PaymentPlanRequest;
use StackPay\Payments\Structures;
use StackPay\Payments\Translators;

class PaymentPlanRequestUnitTest extends UnitTestCase
{
    public function setUp()
    {
        $this->StackPay                = new StackPay\Payments\StackPay('public-12345', 'private-12345');
        $this->merchant                = new Structures\Merchant(13, 'merchant_hash_key_123');
        $this->paymentMethod           = (new Structures\PaymentMethod())->setID(1000);
        $this->paymentPlan             = (new Structures\PaymentPlan())
                                            ->setID(12345)
                                            ->setMerchant($this->merchant);
        $this->subscription            = (new Structures\Subscription())
                                            ->setID(555)
                                            ->setPaymentPlan($this->paymentPlan)
                                            ->setPaymentMethod($this->paymentMethod)
                                            ->setExternalId('1000')
                                            ->setAmount(10000)
                                            ->setDownPaymentAmount(1500)
                                            ->setDay(1);

        $this->request = new PaymentPlanRequest(
            $this->paymentPlan,
            $this->subscription
        );
    }

    public function testConstructor()
    {
        $this->assertEquals($this->request->paymentPlan, $this->paymentPlan);
    }

    public function testCopyPaymentPlan()
    {
        $mockedCopyPaymentPlanElement = [
            'fakeKey1' => 'fakeValue1',
            'fakeKey2' => 'fakeValue2',
            'fakeKey3' => 'fakeValue3',
        ];

        $mockedRestTranslator = Mockery::mock(Translators\V1RESTTranslator::class);
        $mockedRestTranslator->shouldReceive('buildPaymentPlanCopyElement')->once()
            ->with($this->paymentPlan)
            ->andReturn($mockedCopyPaymentPlanElement);

        $this->request->restTranslator = $mockedRestTranslator;

        $createRequest = $this->request->copyPaymentPlan();

        $this->assertEquals($createRequest->method, 'POST');
        $this->assertEquals($createRequest->endpoint, '/api/merchants/' . $this->paymentPlan->merchant->id . '/payment-plans');
        $this->assertEquals($createRequest->hashKey, $this->merchant->hashKey);
        $this->assertEquals($createRequest->body, $mockedCopyPaymentPlanElement);
    }

    public function testGetMerchantPaymentPlans()
    {
        $getRequest = $this->request->getMerchantPaymentPlans();

        $this->assertEquals($getRequest->method, 'GET');
        $this->assertEquals($getRequest->endpoint, '/api/merchants/'. $this->paymentPlan->merchant->id . '/payment-plans');
        $this->assertEquals($getRequest->hashKey, $this->merchant->hashKey);
        $this->assertNull($getRequest->body);
    }

    public function testGetDefaultPaymentPlans()
    {
        $getRequest = $this->request->getDefaultPaymentPlans();

        $this->assertEquals($getRequest->method, 'GET');
        $this->assertEquals($getRequest->endpoint, '/api/payment-plans');
        $this->assertEquals($getRequest->hashKey, $getRequest->hashKey);
        $this->assertNull($getRequest->body);
    }

    public function testCreatePaymentPlanSubscription()
    {
        $response = [
            'fakeKey1' => 'fakeValue1',
            'fakeKey2' => 'fakeValue2',
            'fakeKey3' => 'fakeValue3',
        ];

        $translator = Mockery::mock(Translators\V1RESTTranslator::class);
        $translator->shouldReceive('buildPaymentPlanCreateSubscriptionElement')->once()
            ->with($this->subscription)
            ->andReturn($response);

        $this->request->restTranslator = $translator;

        $request = $this->request->createPaymentPlanSubscription();

        $this->assertEquals($request->method, 'POST');
        $this->assertEquals(
            $request->endpoint,
              '/api'
            . '/merchants/' . $this->paymentPlan->merchant->id
            . '/payment-plans/' . $this->paymentPlan->id
            . '/subscriptions'
        );
        $this->assertEquals($request->hashKey, $this->merchant->hashKey);
        $this->assertEquals($request->body, $response);
    }

    public function testEditPaymentPlanSubscription()
    {
        $this->paymentMethod->setID(9999);
        $response = [
            'paymentMethod' => ['id' => '9999']
        ];

        $translator = Mockery::mock(Translators\V1RESTTranslator::class);
        $translator->shouldReceive('buildPaymentPlanCreateSubscriptionElement')->once()
            ->with($this->subscription)
            ->andReturn($response);

        $this->request->restTranslator = $translator;

        $request = $this->request->editPaymentPlanSubscription();

        $this->assertEquals($request->method, 'PUT');
        $this->assertEquals(
            $request->endpoint,
              '/api'
            . '/merchants/' . $this->subscription->paymentPlan->merchant->id
            . '/payment-plans/' . $this->subscription->paymentPlan->id
            . '/subscriptions/' . $this->subscription->id
        );
        $this->assertEquals($request->body['paymentMethod']['id'], $this->paymentMethod->id);
    }

    // TODO: testEditPaymentPlan when other tests are updated.
    
}
