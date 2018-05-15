<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Address implements Interfaces\Address
{
    public $addressLines = [];
    public $address1;
    public $address2;
    public $city;
    public $state;
    public $postalCode;
    public $country;

    public function __construct(
        $addressLine1 = null,
        $addressLine2 = null,
        $city = null,
        $state = null,
        $postalCode = null,
        $country = null
    ) {
        $this->setAddress1($addressLine1);
        $this->setAddress2($addressLine2);
        $this->setCity($city);
        $this->setState($state);
        $this->setPostalCode($postalCode);
        $this->setCountry($country);
    }

    public function addressLines()
    {
        return $this->addressLines;
    }

    public function address1()
    {
        if (array_key_exists(0, $this->addressLines)) {
            $this->address1 = $this->addressLines[0];
        }

        return $this->address1;
    }

    public function address2()
    {
        if (array_key_exists(1, $this->addressLines)) {
            $this->address2 = $this->addressLines[1];
        }

        return $this->address2;
    }

    public function city()
    {
        return $this->city;
    }

    public function state()
    {
        return $this->state;
    }

    public function postalCode()
    {
        return $this->postalCode;
    }

    public function country()
    {
        return $this->country;
    }

    //--------

    public function setAddressLines($addressLines, $lineDelimiter)
    {
        $this->addressLines = explode($lineDelimiter, $addressLines);

        return $this;
    }

    public function setAddress1($address1 = null)
    {
        $this->addressLines[0] = $address1;
        $this->address1 = $address1;

        return $this;
    }

    public function setAddress2($address2 = null)
    {
        $this->addressLines[1] = $address2;
        $this->address2 = $address2;

        return $this;
    }

    public function setCity($city = null)
    {
        $this->city = $city;

        return $this;
    }

    public function setState($state = null)
    {
        $this->state = $state;

        return $this;
    }

    public function setPostalCode($postalCode = null)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function setCountry($country = null)
    {
        $this->country = $country;

        return $this;
    }
}
