<?php

namespace StackPay\Payments\Interfaces;

interface Customer
{
    public function id();
    public function firstName();
    public function lastName();

    //----------

    public function setID($id = null);
    public function setFirstName($firstName = null);
    public function setLastName($lastName = null);
}
