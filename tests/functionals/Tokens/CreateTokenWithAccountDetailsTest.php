<?php

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\AccountTypes;
use StackPay\Payments\Currency;
use StackPay\Payments\Exceptions;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class CreateTokenWithAccountDetailsTest extends TestCase
{
    public function testCreditCard()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"Hash":"675511ee2aeda6042f2e2afdbadedcc9bbcbeb5ed704da9a23b5b90748655f34"}},"Body":{"Status":1,"Token":"z2CsRkTedDEwI0b"}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $account = (new Structures\Account())
            ->setType(AccountTypes::VISA)
            ->setNumber('4111111111111111')
            ->setCVV2('888')
            ->setExpireDate('0101');

        $address = (new Structures\Address())
            ->setAddress1('1234 Windall Lane')
            ->setCity('Nowhere')
            ->setState('HI')
            ->setPostalCode('89765')
            ->setCountry('usa');

        $accountHolder = (new Structures\AccountHolder())
            ->setName('John Doe')
            ->setBillingAddress($address);

        $token = $sdk->createTokenWithAccountDetails(
            $account,
            $accountHolder
        );

        $this->assertEquals('z2CsRkTedDEwI0b', $token->token());

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/token',
                    'Body' => [
                        'Order' => [
                            'Account'       => [
                                'Type'       => 'visa',
                                'Number'     => '4111111111111111',
                                'ExpireDate' => '0101',
                                'Cvv2'       => '888'
                            ],
                            'AccountHolder' => [
                                'Name'           => 'John Doe',
                                'BillingAddress' => [
                                    'City'     => 'Nowhere',
                                    'State'    => 'HI',
                                    'Zip'      => '89765',
                                    'Country'  => 'usa',
                                    'Address1' => '1234 Windall Lane',
                                    'Address2' => ''
                                ]
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Authorization', 'Value' => 'Bearer 8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea'],
                        1 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testBankAccount()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"Hash":"675511ee2aeda6042f2e2afdbadedcc9bbcbeb5ed704da9a23b5b90748655f34"}},"Body":{"Status":1,"Token":"z2CsRkTedDEwI0b"}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $account = (new Structures\Account())
            ->setType(AccountTypes::SAVINGS)
            ->setNumber('4111111111111111')
            ->setRoutingNumber('8765309');

        $address = (new Structures\Address())
            ->setAddress1('1234 Windall Lane')
            ->setCity('Nowhere')
            ->setState('HI')
            ->setPostalCode('89765')
            ->setCountry('usa');

        $accountHolder = (new Structures\AccountHolder())
            ->setName('John Doe')
            ->setBillingAddress($address);

        $token = $sdk->createTokenWithAccountDetails(
            $account,
            $accountHolder
        );

        $this->assertEquals('z2CsRkTedDEwI0b', $token->token());

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/token',
                    'Body' => [
                        'Order' => [
                            'Account'       => [
                                'Type'          => 'savings',
                                'Number'        => '4111111111111111',
                                'RoutingNumber' => '8765309'
                            ],
                            'AccountHolder' => [
                                'Name'           => 'John Doe',
                                'BillingAddress' => [
                                    'City'     => 'Nowhere',
                                    'State'    => 'HI',
                                    'Zip'      => '89765',
                                    'Country'  => 'usa',
                                    'Address1' => '1234 Windall Lane',
                                    'Address2' => ''
                                ]
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Authorization', 'Value' => 'Bearer 8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea'],
                        1 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }

    public function testInvalidAccountTypeException()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"Hash":"675511ee2aeda6042f2e2afdbadedcc9bbcbeb5ed704da9a23b5b90748655f34"}},"Body":{"Status":1,"Token":"z2CsRkTedDEwI0b"}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $account = (new Structures\Account())
            ->setType('INVALID ACCOUNT TYPE')
            ->setNumber('4111111111111111')
            ->setRoutingNumber('8765309');

        $address = (new Structures\Address())
            ->setAddress1('1234 Windall Lane')
            ->setCity('Nowhere')
            ->setState('HI')
            ->setPostalCode('89765')
            ->setCountry('usa');

        $accountHolder = (new Structures\AccountHolder())
            ->setName('John Doe')
            ->setBillingAddress($address);

        try {
            $token = $sdk->createTokenWithAccountDetails(
                $account,
                $accountHolder
            );
        } catch (Exceptions\InvalidAccountTypeException $e) {
            $this->assertEquals(
                "The supplied AccountType(INVALID ACCOUNT TYPE) is invalid",
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->fail("Unexcepted Exception:\n\t$e->getMessage()");
        }

        $this->assertCount(0, $curlProvider->calls);
    }


    public function testError()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"error_code":304,"error_message":"Gateway validation exception. Invalid card."}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        $sdk->setCurlProvider($curlProvider);

        $account = (new Structures\Account())
            ->setType(AccountTypes::VISA)
            ->setNumber('4111123456780099')
            ->setCVV2('888')
            ->setExpireDate('0101');

        $address = (new Structures\Address())
            ->setAddress1('1234 Windall Lane')
            ->setCity('Nowhere')
            ->setState('HI')
            ->setPostalCode('89765')
            ->setCountry('usa');

        $accountHolder = (new Structures\AccountHolder())
            ->setName('John Doe')
            ->setBillingAddress($address);

        try {
            $token = $sdk->createTokenWithAccountDetails(
                $account,
                $accountHolder
            );
        } catch (Exceptions\RequestErrorException $e) {
            $this->assertEquals($e->getMessage(), 'Gateway validation exception. Invalid card.');
            $this->assertEquals($e->getCode(), 304);
        } catch (\Exception $e) {
            $this->fail('Unexpected exception thrown: '. $e->getMessage());
        }

        $this->assertCount(1, $curlProvider->calls);

        $this->assertEquals(
            [
                0 => [
                    'URL'  => 'https://api.mystackpay.com/api/token',
                    'Body' => [
                        'Order' => [
                            'Account'       => [
                                'Type'       => 'visa',
                                'Number'     => '4111123456780099',
                                'ExpireDate' => '0101',
                                'Cvv2'       => '888'
                            ],
                            'AccountHolder' => [
                                'Name'           => 'John Doe',
                                'BillingAddress' => [
                                    'City'     => 'Nowhere',
                                    'State'    => 'HI',
                                    'Zip'      => '89765',
                                    'Country'  => 'usa',
                                    'Address1' => '1234 Windall Lane',
                                    'Address2' => ''
                                ]
                            ]
                        ]
                    ],
                    'Headers' => [
                        0 => ['Key' => 'Authorization', 'Value' => 'Bearer 8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea'],
                        1 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                    ]
                ]
            ],
            $curlProvider->calls
        );
    }
}
