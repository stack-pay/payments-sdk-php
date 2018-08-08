<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class CustomerRequest extends Request
{
    protected $customer;

    public function __construct(Structures\Customer $customer)
    {
        parent::__construct();

        $this->customer = $customer;
    }

    public function create()
    {
        $this->method   = 'POST';
        $this->endpoint = '/api/customers';
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = $this->translator->buildCustomerElement($this->customer);

        return $this;
    }

    public function delete()
    {
        $this->method   = 'DELETE';
        $this->endpoint = '/api/customers/'. $this->customer->id;
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = null;

        return $this;
    }
}
