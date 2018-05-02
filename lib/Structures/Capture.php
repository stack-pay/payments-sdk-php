<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Capture extends Transaction implements Interfaces\Capture
{
    public $type = 'capture';
    public $auth;
    public $originalTransaction;
    public $capturedTransaction;
    public $authCode;

    public function type()
    {
        return 'Capture';
    }

    public function originalTransaction()
    {
        return $this->originalTransaction;
    }

    public function capturedTransaction()
    {
        return $this->capturedTransaction;
    }

    // ------

    public function setOriginalTransaction(Interfaces\Transaction $originalTransaction = null)
    {
        $this->originalTransaction = $originalTransaction;

        return $this;
    }

    public function setCapturedTransaction(Interfaces\Auth $capturedTransaction = null)
    {
        if ($capturedTransaction) {
            $this->capturedTransaction = $capturedTransaction;
        }
        return $this;
    }

    // ----

    public function createOriginalTransaction()
    {
        if (! $this->originalTransaction) {
            $this->originalTransaction = new Transaction();
        }

        return $this->originalTransaction;
    }

    public function createCapturedTransaction()
    {
        if (! $this->capturedTransaction) {
            $this->capturedTransaction = new Auth();
        }

        return $this->capturedTransaction;
    }
}
