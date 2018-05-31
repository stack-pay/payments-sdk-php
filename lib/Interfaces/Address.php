<?php

namespace StackPay\Payments\Interfaces;

interface Address
{
    public function addressLines();
    public function city();
    public function state();
    public function postalCode();
    public function country();

    //--------

    public function setAddressLines($addressLines, $lineDelimiter);
    public function setAddress1($address1 = null);
    public function setAddress2($address2 = null);
    public function setCity($city = null);
    public function setState($state = null);
    public function setPostalCode($postalCode = null);
    public function setCountry($country = null);
}
