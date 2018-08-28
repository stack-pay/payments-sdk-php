<?php

class PaymentMethodTestCase extends FunctionalTestCase
{
    protected function resourceResponse($include_transactions = false)
    {
        $response = [
            'Status'        => 1,
            'Customer'      => 5434,
            'PaymentMethod' => [
                'ID'                => 12345,
                'AccountType'       => 'visa',
                'AccountLast4'      => 1111,
                'ExpirationMonth'   => 12,
                'ExpirationYear'    => 2025,
                'BillingAddress'    => [
                    'AddressLine1'  => '1234 Test Dr',
                    'AddressLine2'  => null,
                    'City'          => 'Teston',
                    'State'         => 'TX',
                    'Zip'           => 75432,
                    'Country'       => 'USA',
                ],
            ],
        ];

        return $response;
    }
}
