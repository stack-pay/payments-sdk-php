<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;

class PaymentMethodTokenTest extends PaymentMethodTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->token                = new Structures\Token('this-is-a-payment-token');

        $this->paymentMethod        = new Structures\PaymentMethod;
        $this->paymentMethod->token = $this->token;
    }

    public function testToken()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->resourceResponse());

        $request = (new Requests\v1\PaymentMethodRequest($this->paymentMethod))
            ->token();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testTokenWithValidationResponse()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidInputResponse());

        $request = (new Requests\v1\PaymentMethodRequest($this->paymentMethod))
            ->token();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());

        $this->assertEquals($this->response->error()->getCode(), 403);
    }
}
