<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;

class PaymentMethodCreateTest extends PaymentMethodTestCase
{
    public function setUp()
    {
        parent::setUp();

        // create the Account and AccountHolder
        $account                    = new Structures\Account;
        $account->type              = StackPay\Payments\AccountTypes::VISA;
        $account->accountNumber     = '4111111111111111';
        $account->expirationMonth   = '12';
        $account->expirationYear    = '25';
        $account->cvv2              = '999';

        $billingAddress             = new Structures\Address;
        $billingAddress->address1   = '5360 Legacy Drive #150';
        $billingAddress->city       = 'Plano';
        $billingAddress->state      = 'TX';
        $billingAddress->postalCode = '75024';
        $billingAddress->country    = Structures\Country::usa();

        $accountHolder                  = new Structures\AccountHolder;
        $accountHolder->name            = 'Stack Testerman';
        $accountHolder->billingAddress  = $billingAddress;

        // set paymentMethod details
        $this->paymentMethod                = new Structures\PaymentMethod;
        $this->paymentMethod->account       = $account;
        $this->paymentMethod->accountHolder = $accountHolder;
    }

    public function testCreate()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->resourceResponse());

        $request = (new Requests\v1\PaymentMethodRequest($this->paymentMethod))
            ->create();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testCreateWithValidationResponse()
    {
        // mock API success response
        $this->mockApiResponse(422, $this->invalidInputResponse());

        $request = (new Requests\v1\PaymentMethodRequest($this->paymentMethod))
            ->create();

        $this->response = $request->send();

        $this->assertEquals($this->response->status(), 422);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());

        $this->assertEquals($this->response->error()->getCode(), 403);
    }
}
