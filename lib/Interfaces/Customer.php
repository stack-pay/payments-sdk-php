<?php

namespace StackPay\Payments\Interfaces;

interface Customer
{
    public function id();

    //----------

    public function setID($id = null);
}
