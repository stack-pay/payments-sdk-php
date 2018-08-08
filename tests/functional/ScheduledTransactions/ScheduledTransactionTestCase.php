<?php

class ScheduledTransactionTestCase extends FunctionalTestCase
{
    protected function assertResourceResponse()
    {
        parent::assertResourceResponse();

        $this->assertObjectHasAttribute('payment_method', $this->response->body()->data);
        $this->assertObjectHasAttribute('transactions', $this->response->body()->data);
    }

    protected function resourceResponse($include_transactions = false)
    {
        $response = [
            'data' => [
                'id'                => 9682,
                'status'            => 'scheduled',
                'merchant_id'       => 1542,
                'scheduled_at'      => '2018-08-06',
                'external_id'       => 1243058,
                'currency_code'     => 'USD',
                'amount'            => 25000,
                'split_amount'      => null,
                'split_merchant_id' => null,
                'payment_method'    => [
                    'id'                => 12345,
                    'customer_id'       => 7313,
                    'type'              => 'visa',
                    'routing_last_four' => null,
                    'account_last_four' => 1111,
                    'expiration_month'  => 12,
                    'expiration_year'   => 2025,
                    'billing_name'      => 'Billy Testerman',
                    'billing_address_1' => '1234 Test Dr',
                    'billing_address_2' => null,
                    'billing_city'      => 'Teston',
                    'billing_state'     => 'TX',
                    'billing_zip'       => 75432,
                    'billing_country'   => 'USA',
                ],
                'transactions'  => [
                ],
            ]
        ];

        if ($include_transactions) {
            $response['data']['transactions'] = [
            ];
        }

        return $response;
    }

    protected function invalidStatusResponse()
    {
        return [
            'error_code'    => 601,
            'error_message' => 'Scheduled transaction has not been processed yet.',
        ];
    }

    protected function invalidScheduledAtResponse()
    {
        return [
            'error_code'    => 109,
            'error_message' => 'Scheduled payments must be scheduled no earlier than tomorrow (UTC).',
        ];
    }
}
