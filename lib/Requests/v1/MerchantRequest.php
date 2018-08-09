<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class MerchantRequest extends Request
{
    protected $merchant;

    public function __construct(Structures\Merchant $merchant = null)
    {
        parent::__construct();

        $this->merchant = $merchant;
    }

    public function limits(Structures\Merchant $merchant = null)
    {
        if (empty($merchant) && empty($this->merchant)) {
            throw new \Exception('No merchant given. Unable to retrieve limits.');
        }

        $this->method   = 'POST';
        $this->endpoint = '/api/merchants/limits';
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = [
            'Merchant' => $merchant ? $merchant->id ? $this->merchant->id,
        ];

        return $this;
    }

    public function rates()
    {
        $this->method   = 'POST';
        $this->endpoint = '/api/merchants/rates';
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = null;

        return $this;
    }
}
