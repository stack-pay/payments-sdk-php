<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\StackPay;
use StackPay\Payments\AccountTypes;
use StackPay\Payments\Currency;
use StackPay\Payments\Exceptions;
use StackPay\Payments\Modes;
use StackPay\Payments\Structures;

use Test\Mocks\Providers\MockCurlProvider;

final class CreditWithPaymentMethodTest extends TestCase
{
    public function testSucessfulCase()
    {
        $curlProvider = new MockCurlProvider([
            [
                'StatusCode' => 200,
                'Body'       =>
                    '{"Header":{"Security":{"HashMethod":"SHA-256","Hash":"93cc38cfd458af34cacb4fc8f569475d472459b073c36ed15a2a8d268b6e2c17"}},"Body":{"Status":1,"Merchant":21027,"Order":7908,"Transaction":11412,"Credit":{"Customer":58,"PaymentMethod":58,"Amount":100,"Currency":"USD"}}}'
                ,
                'Headers' => []
            ]
        ]);

        $sdk = new StackPay(
            '8a1b9a5ce8d0ea0a05264746c8fa4f2b6c47a034fa40198cce74cd3af62c3dea',
            '83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'
        );

        // $sdk->enableTestMode('http://localhost:9080/');

        $sdk->setCurlProvider($curlProvider);

        $input_amount = 100;
        $input_paymentMethod_id = 58;
        $input_merchant_id = 21027;
        $input_merchant_hashKey = '89035dc63df84a61c957e5ac1bd327344afbb75fceb42e47d9deab85d22dc0b2';

        $paymentMethod = (new Structures\PaymentMethod())
            ->setID($input_paymentMethod_id);

        $merchant =(new Structures\Merchant())
            ->setID($input_merchant_id)
            ->setHashKey($input_merchant_hashKey);

        $credit = $sdk->creditWithPaymentMethod(
            $paymentMethod,
            $merchant,
            $input_amount
        );

        $this->assertEquals([
            "ID"                 => 11412,
            "Status"             => 1,
            "Amount"             => $input_amount,
            "Currency"           => Currency::USD,
            "Merchant" => [
                "ID" => $input_merchant_id,
            ],
            "Order" => [
                "ID" => 7908,
            ],
            "Customer" => [
                "ID" => 58,
            ],
            "Payment Method"     => [
                "ID" => $input_paymentMethod_id,
            ],
        ],[
            "ID"                 => $credit->id(),
            "Status"             => $credit->status(),
            "Amount"             => $credit->amount(),
            "Currency"           => $credit->currency(),
            "Merchant" => [
                "ID" => $credit->merchant()->id(),
            ],
            "Order" => [
                "ID" => $credit->order()->id(),
            ],
            "Customer" => [
                "ID" => $credit->customer()->id(),
            ],
            "Payment Method"     => [
                "ID" => $credit->paymentMethod()->id(),
            ],
        ]);

        $this->assertCount( 1, $curlProvider->calls );

        $this->assertEquals([
            0 => [
                'URL'  => 'https://api.mystackpay.com/api/payments',
                'Body' => [
                    'Body' => [
                        'Merchant' => $input_merchant_id,
                        'Order' => [
                            'PaymentMethod' => $input_paymentMethod_id,
                            'Transaction' => [
                                'Type'          => 'Credit',
                                'Amount'        => $input_amount,
                                'Currency'      => Currency::USD,
                            ],
                        ]
                    ],
                    'Header' => [
                        'Application' => 'PaymentSystem',
                        'ApiVersion'  => 'v1',
                        'Mode'        => 'production',
                        'Security'    => [
                            'HashMethod' => 'SHA-256',
                            'Hash'       => '4a0ed6cc1a4522537f3592c2bf566b47188c127491a11d5b4ffe085eb159d000'
                        ]
                    ]
                ],
                'Headers' => [
                    0 => ['Key' => 'Application',   'Value' => 'PaymentSystem'],
                    1 => ['Key' => 'ApiVersion',    'Value' => 'v1'],
                    2 => ['Key' => 'Mode',          'Value' => 'production'],
                    3 => ['Key' => 'HashMethod',    'Value' => 'SHA-256'],
                    4 => ['Key' => 'Hash',          'Value' => '4a0ed6cc1a4522537f3592c2bf566b47188c127491a11d5b4ffe085eb159d000'],
                    5 => ['Key' => 'Authorization', 'Value' => 'Bearer 83b7d01a5e43fc4cf5130af05018079b603d61c5ad6ab4a4d128a3d0245e9ba5'],
                    6 => ['Key' => 'Content-Type',  'Value' => 'application/json']
                ]
            ]
            ], $curlProvider->calls );
    }
}
