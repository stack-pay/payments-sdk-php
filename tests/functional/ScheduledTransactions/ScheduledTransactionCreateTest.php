<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;

class ScheduledTransactionCreateTest extends ScheduledTransactionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->merchant = new Structures\Merchant(123, 'hashkey_merchant123');
    }

    protected function buildScheduledTransaction($paymentMethod)
    {
        $scheduledTransaction                   = new Structures\ScheduledTransaction();
        $scheduledTransaction->merchant         = $this->merchant;
        $scheduledTransaction->paymentMethod    = $paymentMethod;
        $scheduledTransaction->amount           = 5000;
        $scheduledTransaction->currencyCode     = 'USD';
        $scheduledTransaction->scheduledAt      = new DateTime('first day of next week');
        $scheduledTransaction->externalId       = 'external_id_123';

        return $scheduledTransaction;
    }

    public function testWithPaymentMethodId()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->resourceResponse(), $this->merchant->hashKey);

        // set paymentMethod details
        $paymentMethod      = new Structures\PaymentMethod;
        $paymentMethod->id  = 1234;

        // set scheduledTransaction details
        $scheduledTransaction = $this->buildScheduledTransaction($paymentMethod);

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testWithPaymentMethodToken()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->resourceResponse(), $this->merchant->hashKey);

        // set paymentMethod details
        $paymentMethod          = new Structures\PaymentMethod;
        $paymentMethod->token   = '123aw3ea43r123ewd';

        // set scheduledTransaction details
        $scheduledTransaction = $this->buildScheduledTransaction($paymentMethod);

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testWithPaymentMethodAccountDetails()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->resourceResponse(), $this->merchant->hashKey);

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
        $scheduledTransaction = $this->buildScheduledTransaction($paymentMethod);

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testMerchantInvalid()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidMerchantResponse(), $this->merchant->hashKey);

        // set paymentMethod details
        $paymentMethod      = new Structures\PaymentMethod;
        $paymentMethod->id  = 1234;

        // set scheduledTransaction details
        $scheduledTransaction = $this->buildScheduledTransaction($paymentMethod);

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());
        $this->assertEquals($this->response->error()->getCode(), 406);
    }

    public function testPaymentMethodNotFound()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidPaymentMethodResponse(), $this->merchant->hashKey);

        // set paymentMethod details
        $paymentMethod      = new Structures\PaymentMethod;
        $paymentMethod->id  = 1234;

        // set scheduledTransaction details
        $scheduledTransaction = $this->buildScheduledTransaction($paymentMethod);

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());
        $this->assertEquals($this->response->error()->getCode(), 409);
    }

    public function testTokenNotFound()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidTokenResponse(), $this->merchant->hashKey);

        // set paymentMethod details
        $paymentMethod          = new Structures\PaymentMethod;
        $paymentMethod->token   = 'notarealtoken';

        // set scheduledTransaction details
        $scheduledTransaction = $this->buildScheduledTransaction($paymentMethod);

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());
        $this->assertEquals($this->response->error()->getCode(), 404);
    }

    public function testAccountDetailsInvalid()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidInputResponse(), $this->merchant->hashKey);

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
        $scheduledTransaction = $this->buildScheduledTransaction($paymentMethod);

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());
        $this->assertEquals($this->response->error()->getCode(), 403);
    }

    public function testScheduledAtInvalid()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidScheduledAtResponse(), $this->merchant->hashKey);

        // set paymentMethod details
        $paymentMethod      = new Structures\PaymentMethod;
        $paymentMethod->id  = 1234;

        // set scheduledTransaction details
        $scheduledTransaction               = $this->buildScheduledTransaction($paymentMethod);
        $scheduledTransaction->scheduledAt  = new DateTime('now');

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());
        $this->assertEquals($this->response->error()->getCode(), 109);
    }
}
