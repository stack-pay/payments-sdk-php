<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Customer implements Interfaces\Customer
{
    public $id;

    public function __construct($id = null)
    {
        $this->setID($id);
    }

    public function id()
    {
        return $this->id;
    }

    //------

    public function setID($id = null)
    {
        $this->id = $id;

        return $this;
    }
}
