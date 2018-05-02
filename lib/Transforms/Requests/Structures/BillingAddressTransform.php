<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait BillingAddressTransform
{
    public function requestBillingAddress($billingAddress)
    {
        $body = [
            'City'    => $billingAddress->city(),
            'State'   => $billingAddress->state(),
            'Zip'     => $billingAddress->postalCode(),
            'Country' => $billingAddress->country()
        ];

        $lines = $billingAddress->addressLines();

        if (count($lines) > 0) {
            $body['Address1'] = $lines[0];
        }

        if (count($lines) > 1) {
            $body['Address2'] = $lines[1];
        } else {
            $body['Address2'] = '';
        }

        return $body;
    }
}
