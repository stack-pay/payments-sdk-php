<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class MerchantApplication implements Interfaces\MerchantApplication
{
    public $externalId;
    public $name;
    public $rate;
    public $token;

    public function __construct($token = null)
    {
        $this->setToken($token);
    }

    public function externalId()
    {
        return $this->externalId;
    }

    public function name()
    {
        return $this->name;
    }

    public function rate()
    {
        return $this->rate;
    }

    public function token()
    {
        return $this->token;
    }

    //------

    public function setExternalId($externalId = null)
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    public function setRate($rate = null)
    {
        $this->rate = $rate;

        return $this;
    }

    public function setToken($token = null)
    {
        $this->token = $token;

        return $this;
    }
}
