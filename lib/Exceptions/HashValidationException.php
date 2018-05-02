<?php

namespace StackPay\Payments\Exceptions;

class HashValidationException extends \Exception
{
    protected $message = 'The response failed to pass hash validation.';

    protected $code = '1001';
}
