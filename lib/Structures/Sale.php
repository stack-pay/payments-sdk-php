<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Sale extends Auth implements Interfaces\Sale
{
    public $type;

    public function type()
    {
        return 'Sale';
    }
}
