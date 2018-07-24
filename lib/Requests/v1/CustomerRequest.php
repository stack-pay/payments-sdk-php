<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\StackPay;
use StackPay\Payments\Structures\Customer;

class CustomerRequest extends Request
{
    public static function create(Customer $customer)
    {
        $request = new self();

        $request->method   = 'POST';
        $request->endpoint = '/api/customers';
        $request->hashKey  = StackPay::$privateKey;
        $request->body     = $request->translator->buildCustomerElement($customer);

        return $request;
    }

    public static function delete(Customer $customer)
    {
        $request = new self();

        $request->method   = 'DELETE';
        $request->endpoint = '/api/customers/'. $customer->id;
        $request->hashKey  = StackPay::$privateKey;
        $request->body     = null;

        return $request;
    }
}
