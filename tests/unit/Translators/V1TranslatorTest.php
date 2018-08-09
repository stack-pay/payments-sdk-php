<?php

use StackPay\Payments\Exceptions;
use StackPay\Payments\Structures;
use StackPay\Payments\Translators\V1Translator;

class V1TranslatorTest extends UnitTestCase
{
    public function setUp()
    {
        $this->translator = new V1Translator;
    }

    public function testBuildAccountElementWithBankAccount()
    {
        $account = Mockery::mock(Structures\Account::class)->makePartial();
        $account->shouldReceive('isBankAccount')->once()->andReturn(true);

        $account->type          = 'checking';
        $account->number        = '1234567890';
        $account->routingNumber = '111000025';

        $accountElement = $this->translator->buildAccountElement($account);

        $this->assertEquals(
            [
                'Number'        => $account->number,
                'RoutingNumber' => $account->routingNumber,
            ],
            $accountElement
        );
    }

    public function testBuildAccountElementWithCardAccount()
    {
        $account = Mockery::mock(Structures\Account::class)->makePartial();
        $account->shouldReceive('isBankAccount')->once()->andReturn(false);
        $account->shouldReceive('isCardAccount')->once()->andReturn(true);

        $account->type          = 'visa';
        $account->number        = '4111111111111111';
        $account->expireDate    = '1225';
        $account->cvv2          = '999';

        $accountElement = $this->translator->buildAccountElement($account);

        $this->assertEquals(
            [
                'Number'        => $account->number,
                'ExpireDate'    => $account->expireDate,
                'Cvv2'          => $account->cvv2,
            ],
            $accountElement
        );
    }

    public function testBuildAccountElementWithInvalidType()
    {
        $account = Mockery::mock(Structures\Account::class)->makePartial();
        $account->shouldReceive('isBankAccount')->once()->andReturn(false);
        $account->shouldReceive('isCardAccount')->once()->andReturn(false);

        $account->type  = 'faketype';

        $this->expectException(Exceptions\InvalidAccountTypeException::class);

        $accountElement = $this->translator->buildAccountElement($account);
    }

    public function testBuildAccountHolderElement()
    {
        $address                = new Structures\Address;
        $address->address1      = '5360 Legacy Drive';
        $address->address2      = 'Suite 150';
        $address->city          = 'Plano';
        $address->state         = 'TX';
        $address->postalCode    = '75024';
        $address->country       = 'USA';

        $accountHolder                  = new Structures\AccountHolder;
        $accountHolder->name            = 'Stack Payerman';
        $accountHolder->billingAddress  = $address;

        $mockedAddressElement = [
            'Address1'  => $address->address1,
            'Address2'  => $address->address2,
            'City'      => $address->city,
            'State'     => $address->state,
            'Zip'       => $address->postalCode,
            'Country'   => $address->country,
        ];

        $translatorPartial = Mockery::mock(V1Translator::class)->makePartial();
        $translatorPartial->shouldReceive('buildAddressElement')->once()
            ->with($accountHolder->billingAddress)
            ->andReturn($mockedAddressElement);

        $accountHolderElement = $translatorPartial->buildAccountHolderElement($accountHolder);

        $this->assertEquals(
            [
                'Name'              => $accountHolder->name,
                'BillingAddress'    => $mockedAddressElement,
            ],
            $accountHolderElement
        );
    }

    public function testBuildAddressElement()
    {
        $address                = new Structures\Address;
        $address->address1      = '5360 Legacy Drive';
        $address->address2      = 'Suite 150';
        $address->city          = 'Plano';
        $address->state         = 'TX';
        $address->postalCode    = '75024';
        $address->country       = 'USA';

        $addressElement = $this->translator->buildAddressElement($address);

        $this->assertEquals(
            [
                'Address1'  => $address->address1,
                'Address2'  => $address->address2,
                'City'      => $address->city,
                'State'     => $address->state,
                'Zip'       => $address->postalCode,
                'Country'   => $address->country,
            ],
            $addressElement
        );
    }

    public function testBuildMerchantApplicationElement()
    {
        $merchantApplication = new Structures\MerchantApplication;
        $merchantApplication->externalId    = 'external-system-id';
        $merchantApplication->rate          = 'Rate A';
        $merchantApplication->name          = 'Test Merchant Application';

        $merchantApplicationElement = $this->translator->buildMerchantApplicationElement($merchantApplication);

        $this->assertEquals(
            [
                'ExternalId'        => $merchantApplication->externalId,
                'RateName'          => $merchantApplication->rate,
                'ApplicationName'   => $merchantApplication->name,
            ],
            $merchantApplicationElement
        );
    }

    public function testBuildTokenElement()
    {
        $token          = new Structures\Token;
        $token->token   = 'payment-method-token';

        $tokenElement = $this->translator->buildTokenElement($token);

        $this->assertEquals(
            [
                'Token' => $token->token
            ],
            $tokenElement
        );
    }
}
