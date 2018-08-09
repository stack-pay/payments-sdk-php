<?php

use StackPay\Payments\Exceptions;
use StackPay\Payments\Structures;
use StackPay\Payments\Translators\V1RESTTranslator;

class V1RESTTranslatorTest extends UnitTestCase
{
    public function setUp()
    {
        $this->translator = new V1RESTTranslator;
    }

    public function testBuildAccountHolderElement()
    {
        $paymentMethod                  = new Structures\PaymentMethod;
        $paymentMethod->billingName     = 'Stack Payerman';
        $paymentMethod->billingAddress1 = '5360 Legacy Drive';
        $paymentMethod->billingCity     = 'Plano';
        $paymentMethod->billingState    = 'TX';
        $paymentMethod->billingZip      = '75024';
        $paymentMethod->billingCountry  = 'USA';

        $accountHolderElement = $this->translator->buildAccountHolderElement($paymentMethod);

        $this->assertEquals(
            [
                'billing_name'      => $paymentMethod->billingName,
                'billing_address_1' => $paymentMethod->billingAddress1,
                'billing_city'      => $paymentMethod->billingCity,
                'billing_state'     => $paymentMethod->billingState,
                'billing_zip'       => $paymentMethod->billingZip,
                'billing_country'   => $paymentMethod->billingCountry,
            ],
            $accountHolderElement
        );
    }

    public function testBuildCustomerElement()
    {
        $customer               = new Structures\Customer;
        $customer->firstName    = 'Stack';
        $customer->lastName     = 'Payerman';

        $customerElement = $this->translator->buildCustomerElement($customer);

        $this->assertEquals(
            [
                'first_name'    => $customer->firstName,
                'last_name'     => $customer->lastName,
            ],
            $customerElement
        );
    }

    public function testBuildPaymentMethodElementAsId()
    {
        $paymentMethod      = new Structures\PaymentMethod;
        $paymentMethod->id  = 12345;

        $paymentMethodElement = $this->translator->buildPaymentMethodElementAsId($paymentMethod);

        $this->assertEquals(
            [
                'method'    => 'id',
                'id'        => $paymentMethod->id,
            ],
            $paymentMethodElement
        );
    }

    public function testBuildPaymentMethodElementWithId()
    {
        $paymentMethodWithId     = new Structures\PaymentMethod;
        $paymentMethodWithId->id = 12345;

        $mockedPaymentMethodElement = [
            'method'    => 'id',
            'id'        => 12345,
        ];

        $translatorPartial = Mockery::mock(V1RESTTranslator::class)->makePartial();
        $translatorPartial->shouldReceive('buildPaymentMethodElementAsId')->once()
            ->with($paymentMethodWithId)
            ->andReturn($mockedPaymentMethodElement);

        $paymentMethodElement = $translatorPartial->buildPaymentMethodElement($paymentMethodWithId);

        $this->assertEquals($paymentMethodElement, $mockedPaymentMethodElement);
    }

    public function testBuildPaymentMethodElementAsToken()
    {
        $paymentMethod          = new Structures\PaymentMethod;
        $paymentMethod->token   = 'payment-method-token';

        $paymentMethodElement = $this->translator->buildPaymentMethodElementAsToken($paymentMethod);

        $this->assertEquals(
            [
                'method'    => 'token',
                'token'     => $paymentMethod->token,
            ],
            $paymentMethodElement
        );
    }

    public function testBuildPaymentMethodElementWithToken()
    {
        $paymentMethodWithToken         = new Structures\PaymentMethod;
        $paymentMethodWithToken->token  = 'payment-method-token';

        $mockedPaymentMethodElement = [
            'method'    => 'token',
            'token'     => 'payment-method-token',
        ];

        $translatorPartial = Mockery::mock(V1RESTTranslator::class)->makePartial();
        $translatorPartial->shouldReceive('buildPaymentMethodElementAsToken')->once()
            ->with($paymentMethodWithToken)
            ->andReturn($mockedPaymentMethodElement);

        $paymentMethodElement = $translatorPartial->buildPaymentMethodElement($paymentMethodWithToken);

        $this->assertEquals($paymentMethodElement, $mockedPaymentMethodElement);
    }

    public function testBuildPaymentMethodElementAsBankAccount()
    {
        $paymentMethod                  = new Structures\PaymentMethod;
        $paymentMethod->type            = 'checking';
        $paymentMethod->accountNumber   = '1234567890';
        $paymentMethod->routingNumber   = '111000025';
        $paymentMethod->billingName     = 'Stack Payerman';
        $paymentMethod->billingAddress1 = '5360 Legacy Drive';
        $paymentMethod->billingCity     = 'Plano';
        $paymentMethod->billingState    = 'TX';
        $paymentMethod->billingZip      = '75024';
        $paymentMethod->billingCountry  = 'USA';

        $mockedAccountHolderElement = [
            'billing_name'      => $paymentMethod->billingName,
            'billing_address_1' => $paymentMethod->billingAddress1,
            'billing_city'      => $paymentMethod->billingCity,
            'billing_state'     => $paymentMethod->billingState,
            'billing_zip'       => $paymentMethod->billingZip,
            'billing_country'   => $paymentMethod->billingCountry,
        ];

        $translatorPartial = Mockery::mock(V1RESTTranslator::class)->makePartial();
        $translatorPartial->shouldReceive('buildAccountHolderElement')->once()
            ->with($paymentMethod)
            ->andReturn($mockedAccountHolderElement);

        $bankAccountElement = $translatorPartial->buildPaymentMethodElementAsBankAccount($paymentMethod);

        $this->assertEquals(
            [
                'method'            => 'bank_account',
                'type'              => $paymentMethod->type,
                'account_number'    => $paymentMethod->accountNumber,
                'routing_number'    => $paymentMethod->routingNumber,
                'billing_name'      => $paymentMethod->billingName,
                'billing_address_1' => $paymentMethod->billingAddress1,
                'billing_city'      => $paymentMethod->billingCity,
                'billing_state'     => $paymentMethod->billingState,
                'billing_zip'       => $paymentMethod->billingZip,
                'billing_country'   => $paymentMethod->billingCountry,
            ],
            $bankAccountElement
        );
    }

    public function testBuildPaymentMethodElementWithBankAccount()
    {
        $paymentMethodWithBankAccount       = Mockery::mock(Structures\PaymentMethod::class)->makePartial();
        $paymentMethodWithBankAccount->type = 'checking';

        $paymentMethodWithBankAccount->shouldReceive('isBankAccount')->once()->andReturn(true);

        $mockedPaymentMethodElement = [
            'method'    => 'bank_account',
            'type'      => 'checking',
        ];

        $translatorPartial = Mockery::mock(V1RESTTranslator::class)->makePartial();
        $translatorPartial->shouldReceive('buildPaymentMethodElementAsBankAccount')->once()
            ->with($paymentMethodWithBankAccount)
            ->andReturn($mockedPaymentMethodElement);

        $paymentMethodElement = $translatorPartial->buildPaymentMethodElement($paymentMethodWithBankAccount);

        $this->assertEquals($paymentMethodElement, $mockedPaymentMethodElement);
    }

    public function testBuildPaymentMethodElementAsCardAccount()
    {
        $paymentMethod                  = new Structures\PaymentMethod;
        $paymentMethod->type            = 'visa';
        $paymentMethod->accountNumber   = '4111111111111111';
        $paymentMethod->cvv2            = '999';
        $paymentMethod->expirationMonth = '12';
        $paymentMethod->expirationYear  = '25';
        $paymentMethod->billingName     = 'Stack Payerman';
        $paymentMethod->billingAddress1 = '5360 Legacy Drive';
        $paymentMethod->billingCity     = 'Plano';
        $paymentMethod->billingState    = 'TX';
        $paymentMethod->billingZip      = '75024';
        $paymentMethod->billingCountry  = 'USA';

        $mockedAccountHolderElement = [
            'billing_name'      => $paymentMethod->billingName,
            'billing_address_1' => $paymentMethod->billingAddress1,
            'billing_city'      => $paymentMethod->billingCity,
            'billing_state'     => $paymentMethod->billingState,
            'billing_zip'       => $paymentMethod->billingZip,
            'billing_country'   => $paymentMethod->billingCountry,
        ];

        $translatorPartial = Mockery::mock(V1RESTTranslator::class)->makePartial();
        $translatorPartial->shouldReceive('buildAccountHolderElement')->once()
            ->with($paymentMethod)
            ->andReturn($mockedAccountHolderElement);

        $cardAccountElement = $translatorPartial->buildPaymentMethodElementAsCardAccount($paymentMethod);

        $this->assertEquals(
            [
                'method'            => 'credit_card',
                'type'              => $paymentMethod->type,
                'account_number'    => $paymentMethod->accountNumber,
                'cvv2'              => $paymentMethod->cvv2,
                'expiration_month'  => $paymentMethod->expirationMonth,
                'expiration_year'   => $paymentMethod->expirationYear,
                'billing_name'      => $paymentMethod->billingName,
                'billing_address_1' => $paymentMethod->billingAddress1,
                'billing_city'      => $paymentMethod->billingCity,
                'billing_state'     => $paymentMethod->billingState,
                'billing_zip'       => $paymentMethod->billingZip,
                'billing_country'   => $paymentMethod->billingCountry,
            ],
            $cardAccountElement
        );
    }

    public function testBuildPaymentMethodElementWithCardAccount()
    {
        $paymentMethodWithCardAccount       = Mockery::mock(Structures\PaymentMethod::class)->makePartial();
        $paymentMethodWithCardAccount->type = 'visa';

        $paymentMethodWithCardAccount->shouldReceive('isBankAccount')->once()->andReturn(false);
        $paymentMethodWithCardAccount->shouldReceive('isCardAccount')->once()->andReturn(true);

        $mockedPaymentMethodElement = [
            'method'    => 'credit_card',
            'type'      => 'visa',
        ];

        $translatorPartial = Mockery::mock(V1RESTTranslator::class)->makePartial();
        $translatorPartial->shouldReceive('buildPaymentMethodElementAsCardAccount')->once()
            ->with($paymentMethodWithCardAccount)
            ->andReturn($mockedPaymentMethodElement);

        $paymentMethodElement = $translatorPartial->buildPaymentMethodElement($paymentMethodWithCardAccount);

        $this->assertEquals($paymentMethodElement, $mockedPaymentMethodElement);
    }

    public function testBuildPaymentMethodElementWithInvalidType()
    {
        $paymentMethodWithInvalidType       = Mockery::mock(Structures\PaymentMethod::class)->makePartial();
        $paymentMethodWithInvalidType->type = 'faketype';

        $paymentMethodWithInvalidType->shouldReceive('isBankAccount')->once()->andReturn(false);
        $paymentMethodWithInvalidType->shouldReceive('isCardAccount')->once()->andReturn(false);

        $this->expectException(Exceptions\InvalidAccountTypeException::class);

        $paymentMethodElement = $this->translator->buildPaymentMethodElement($paymentMethodWithInvalidType);
    }

    public function testBuildScheduledTransactionElement()
    {
        $merchant = new Structures\Merchant(13, 'merchant-13-hash-key');

        $paymentMethod                  = new Structures\PaymentMethod;
        $paymentMethod->type            = 'checking';
        $paymentMethod->accountNumber   = '1234567890';
        $paymentMethod->routingNumber   = '111000025';
        $paymentMethod->billingName     = 'Stack Payerman';
        $paymentMethod->billingAddress1 = '5360 Legacy Drive';
        $paymentMethod->billingCity     = 'Plano';
        $paymentMethod->billingState    = 'TX';
        $paymentMethod->billingZip      = '75024';
        $paymentMethod->billingCountry  = 'USA';

        $scheduledTransaction                   = new Structures\ScheduledTransaction;
        $scheduledTransaction->externalId       = 'external-system-id';
        $scheduledTransaction->merchant         = $merchant;
        $scheduledTransaction->scheduledAt      = new DateTime('second friday');
        $scheduledTransaction->currencyCode     = 'USD';
        $scheduledTransaction->amount           = 5000;
        $scheduledTransaction->paymentMethod    = $paymentMethod;

        $mockedPaymentMethodElement = [
            'method'            => 'credit_card',
            'type'              => $paymentMethod->type,
            'account_number'    => $paymentMethod->accountNumber,
            'cvv2'              => $paymentMethod->cvv2,
            'expiration_month'  => $paymentMethod->expirationMonth,
            'expiration_year'   => $paymentMethod->expirationYear,
            'billing_name'      => $paymentMethod->billingName,
            'billing_address_1' => $paymentMethod->billingAddress1,
            'billing_city'      => $paymentMethod->billingCity,
            'billing_state'     => $paymentMethod->billingState,
            'billing_zip'       => $paymentMethod->billingZip,
            'billing_country'   => $paymentMethod->billingCountry,
        ];

        $translatorPartial = Mockery::mock(V1RESTTranslator::class)->makePartial();
        $translatorPartial->shouldReceive('buildPaymentMethodElement')->once()
            ->with($scheduledTransaction->paymentMethod)
            ->andReturn($mockedPaymentMethodElement);

        $scheduledTransactionElement = $translatorPartial->buildScheduledTransactionElement($scheduledTransaction);

        $this->assertEquals(
            [
                'external_id'       => $scheduledTransaction->externalId,
                'merchant_id'       => $scheduledTransaction->merchant->id,
                'scheduled_at'      => $scheduledTransaction->scheduledAt->format('Y-m-d'),
                'currency_code'     => $scheduledTransaction->currencyCode,
                'amount'            => $scheduledTransaction->amount,
                'payment_method'    => $mockedPaymentMethodElement,
            ],
            $scheduledTransactionElement
        );
    }
}
