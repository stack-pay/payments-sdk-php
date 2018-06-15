<?php

use StackPay\Payments\Structures;

final class TokenTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\Token::class;

    public function test_token()
    {
        $this->full('token', 'string', false);
    }
}
