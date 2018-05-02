<?php

namespace StackPay\Payments\Interfaces;

interface Order
{
    public function id();

    //-----------

    public function setID($id = null);
}
