<?php

namespace StackPay\Payments\Structures;

class Country
{
    const AUS   = 'AUS';
    const CAN   = 'CAN';
    const USA   = 'USA';

    public static function australia()
    {
        return 'AUS';
    }

    public static function canada()
    {
        return 'CAN';
    }

    public static function usa()
    {
        return 'USA';
    }
}
