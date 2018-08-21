<?php

namespace StackPay\Payments\Exceptions;

class RequestErrorException extends \Exception
{
    protected $message = 'An error occurred while attempting the request.';

    protected $code = '1002';

    protected $errors;

    public function errors()
    {
        return $this->errors;
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $errors;
    }
}
