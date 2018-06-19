<?php

namespace StackPay\Payments\Responses\v1;

class ErrorResponse
{
    protected $code;
    protected $message;
    protected $errors;

    public function __construct($code, $message, $errors = null)
    {
        $this->code     = $code;
        $this->message  = $message;
        $this->errors   = $errors;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
