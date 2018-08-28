<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;

class ScheduledTransactionRetryTest extends ScheduledTransactionTestCase
{
    public function testWithPaymentMethodId()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->resourceResponse());

        // set paymentMethod details
        $paymentMethod      = new Structures\PaymentMethod;
        $paymentMethod->id  = 1234;

        // set scheduledTransaction details
        $scheduledTransaction                   = new Structures\ScheduledTransaction;
        $scheduledTransaction->id               = 123;
        $scheduledTransaction->paymentMethod    = $paymentMethod;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testWithPaymentMethodToken()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->resourceResponse());

        // set paymentMethod details
        $paymentMethod          = new Structures\PaymentMethod;
        $paymentMethod->token   = '123aw3ea43r123ewd';

        // set scheduledTransaction details
        $scheduledTransaction                   = new Structures\ScheduledTransaction;
        $scheduledTransaction->id               = 123;
        $scheduledTransaction->paymentMethod    = $paymentMethod;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testWithPaymentMethodAccountDetails()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->resourceResponse());

        // set paymentMethod details
        $paymentMethod                  = new Structures\PaymentMethod;
        $paymentMethod->type            = StackPay\Payments\AccountTypes::VISA;
        $paymentMethod->accountNumber   = '4111111111111111';
        $paymentMethod->expirationMonth = '12';
        $paymentMethod->expirationYear  = '25';
        $paymentMethod->cvv2            = '999';
        $paymentMethod->billingName     = 'Stack Testerman';
        $paymentMethod->billingAddress1 = '5360 Legacy Drive #150';
        $paymentMethod->billingCity     = 'Plano';
        $paymentMethod->billingState    = 'TX';
        $paymentMethod->billingZip      = '75024';
        $paymentMethod->billingCountry  = Structures\Country::usa();

        // set scheduledTransaction details
        $scheduledTransaction                   = new Structures\ScheduledTransaction;
        $scheduledTransaction->id               = 123;
        $scheduledTransaction->paymentMethod    = $paymentMethod;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testNotFound()
    {
        // mock API success response
        $this->mockApiResponse(404, $this->notFoundResponse());

        // set scheduledTransaction details
        $scheduledTransaction       = new Structures\ScheduledTransaction;
        $scheduledTransaction->id   = 123;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

        $this->response = $request->send();

        $this->assertResourceNotFoundResponse();
    }

    public function testPaymentMethodNotFound()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidPaymentMethodResponse());

        // set paymentMethod details
        $paymentMethod      = new Structures\PaymentMethod;
        $paymentMethod->id  = 1234;

        // set scheduledTransaction details
        $scheduledTransaction                   = new Structures\ScheduledTransaction;
        $scheduledTransaction->id               = 123;
        $scheduledTransaction->paymentMethod    = $paymentMethod;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());
        $this->assertEquals($this->response->error()->getCode(), 409);
    }

    public function testTokenNotFound()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidTokenResponse());

        // set paymentMethod details
        $paymentMethod          = new Structures\PaymentMethod;
        $paymentMethod->token   = 'notarealtoken';

        // set scheduledTransaction details
        $scheduledTransaction                   = new Structures\ScheduledTransaction;
        $scheduledTransaction->id               = 123;
        $scheduledTransaction->paymentMethod    = $paymentMethod;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());
        $this->assertEquals($this->response->error()->getCode(), 404);
    }

    public function testAccountDetailsInvalid()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidInputResponse());

        // set paymentMethod details
        $paymentMethod                      = new Structures\PaymentMethod;
        $paymentMethod->type                = StackPay\Payments\AccountTypes::VISA;
        $paymentMethod->accountNumber       = '4111111111111111111'; // too many digits
        $paymentMethod->expirationMonth     = '12';
        $paymentMethod->expirationYear      = '25';
        $paymentMethod->cvv2                = '999';
        $paymentMethod->billingName         = 'Stack Testerman';
        $paymentMethod->billingAddress1     = '5360 Legacy Drive #150';
        $paymentMethod->billingCity         = 'Plano';
        $paymentMethod->billingState        = 'TX';
        $paymentMethod->billingZip          = '75024';
        $paymentMethod->billingCountry      = Structures\Country::USA;

        // set scheduledTransaction details
        $scheduledTransaction                   = new Structures\ScheduledTransaction;
        $scheduledTransaction->id               = 123;
        $scheduledTransaction->paymentMethod    = $paymentMethod;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());
        $this->assertEquals($this->response->error()->getCode(), 403);
    }

    public function testInvalidStatus()
    {
        // mock API success response
        $this->mockApiResponse(409, $this->invalidStatusResponse());

        // set scheduledTransaction details
        $scheduledTransaction       = new Structures\ScheduledTransaction;
        $scheduledTransaction->id   = 123;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 409);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());
        $this->assertEquals($this->response->error()->getCode(), 601);
    }

    public function testPaymentFailed()
    {
        // mock API success response
        $resourceResponse = $this->resourceResponse();
        $resourceResponse['data']['status'] = 'failed';
        $this->mockApiResponse(200, $resourceResponse);

        // set scheduledTransaction details
        $scheduledTransaction       = new Structures\ScheduledTransaction;
        $scheduledTransaction->id   = 123;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

        $this->response = $request->send();

        $this->assertResourceResponse();
        $this->assertEquals($this->response->body()->data->status, 'failed');
    }
}
