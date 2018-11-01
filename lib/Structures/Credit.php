<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Credit extends Transaction implements Interfaces\Credit
{
    public $type = 'Credit';

    public function type()
    {
        return 'Credit';
    }
}
