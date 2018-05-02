<?php

namespace StackPay\Payments\Exceptions;

class RequestErrorException extends \Exception
{
    protected $message = 'An error occurred while attempting the request.';

    protected $code = '1002';

    protected $errors;

    public function message()
    {
        return $this->message;
    }

    public function code()
    {
        return $this->code;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $errors;
    }
}
