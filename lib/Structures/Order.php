<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Order implements Interfaces\Order
{
    public $id;

    public function id()
    {
        return $this->id;
    }

    // --------

    public function setID($id = null)
    {
        $this->id = $id;

        return $this;
    }
}
