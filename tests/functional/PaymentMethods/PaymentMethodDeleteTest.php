<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;

class PaymentMethodDeleteTest extends PaymentMethodTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->paymentMethod = new Structures\PaymentMethod(123);
    }

    public function testFound()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->emptyResponse());

        $request = (new Requests\v1\PaymentMethodRequest($this->paymentMethod))
            ->delete();

        $this->response = $request->send();

        $this->assertEmptyResponse();
    }

    public function testNotFound()
    {
        // mock API success response
        $this->mockApiResponse(404, $this->notFoundResponse());

        $request = (new Requests\v1\PaymentMethodRequest($this->paymentMethod))
            ->delete();

        $this->response = $request->send();

        $this->assertResourceNotFoundResponse();
    }
}
