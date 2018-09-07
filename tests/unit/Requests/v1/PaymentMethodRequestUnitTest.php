<?php

use StackPay\Payments\Requests\v1\PaymentMethodRequest;
use StackPay\Payments\Structures;
use StackPay\Payments\Translators;

class PaymentMethodRequestUnitTest extends UnitTestCase
{
    public function setUp()
    {
        $this->StackPay         = new StackPay\Payments\StackPay('public-12345', 'private-12345');

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

        $this->paymentMethod->token         = new Structures\Token('this-is-a-payment-token'); // for testToken()
        $this->paymentMethod->id            = 4321; // for testDelete()

        $this->request          = new PaymentMethodRequest($this->paymentMethod);
    }

    public function testConstructor()
    {
        $this->assertEquals($this->request->paymentMethod, $this->paymentMethod);
    }

    public function testCreate()
    {
        $mockedAccountElement = [
            'fakeKey1' => 'fakeValue1',
        ];

        $mockedAccountHolderElement = [
            'fakeKey2' => 'fakeValue2',
            'fakeKey3' => 'fakeValue3',
        ];

        $mockedTranslator = Mockery::mock(Translators\V1Translator::class);
        $mockedTranslator->shouldReceive('buildAccountElement')->once()
            ->with($this->paymentMethod->account)
            ->andReturn($mockedAccountElement);
        $mockedTranslator->shouldReceive('buildAccountHolderElement')->once()
            ->with($this->paymentMethod->accountHolder)
            ->andReturn($mockedAccountHolderElement);

        $this->request->translator = $mockedTranslator;

        $createRequest = $this->request->create();

        $this->assertEquals($createRequest->method, 'POST');
        $this->assertEquals($createRequest->endpoint, '/api/paymethods');
        $this->assertEquals($createRequest->hashKey, $this->StackPay::$privateKey);
        $this->assertEquals($createRequest->body, [
            'Account'       => $mockedAccountElement,
            'AccountHolder' => $mockedAccountHolderElement,
        ]);
    }

    public function testToken()
    {
        $mockedTokenElement = [
            'fakeKey1' => 'fakeValue1',
        ];

        $mockedTranslator = Mockery::mock(Translators\V1Translator::class);
        $mockedTranslator->shouldReceive('buildTokenElement')->once()
            ->with($this->paymentMethod->token)
            ->andReturn($mockedTokenElement);

        $this->request->translator = $mockedTranslator;

        $tokenRequest = $this->request->token();

        $this->assertEquals($tokenRequest->method, 'POST');
        $this->assertEquals($tokenRequest->endpoint, '/api/paymethods');
        $this->assertEquals($tokenRequest->hashKey, $this->StackPay::$privateKey);
        $this->assertEquals($tokenRequest->body, $mockedTokenElement);
    }

    public function testDelete()
    {
        $deleteRequest = $this->request->delete();

        $this->assertEquals($deleteRequest->method, 'DELETE');
        $this->assertEquals($deleteRequest->endpoint, '/api/payment-methods/'. $this->paymentMethod->id);
        $this->assertEquals($deleteRequest->hashKey, $this->StackPay::$privateKey);
        $this->assertNull($deleteRequest->body);
    }
}
